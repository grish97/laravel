class Request
{
    getData (url,form) {
        let formData = new FormData(form);

        $.ajax({
            url : url,
            data : formData,
            type : "post",
            dataType : 'json',
            contentType: false,
            processData: false,
            success : (data) =>
            {

                if(data.length === 0) {
                    console.log('Empty Data');
                    $(form).find(`#name`).val(``);
                    return false;
                }
                request.generateView(data);
            },
            error : (data) =>  {
                console.log(data.responseText);
            },
        });
    }

    fillSelect (elemId,id) {
        $.ajax({
            url : '/fill-select',
            method : 'post',
            data : {
              elemId : elemId,
              id : id,
            },
            dataType : 'json',
        }).done(function(data) {
            request.generateSelect(data,elemId);
        });
    }

    generateSelect(data,elemId) {
        let makeSelect = $(`#make`),
            yearSelect = $(`#year`),
            modelSelect = $(`#model`);

        if(elemId === 'make') {
            modelSelect.empty();
            yearSelect.empty();

            $.each(data,(key,value) => {
                let optionModel = `<option value="${value.model.id}">${value.model.name}</option>`;
                let optionYear = `<option value="">2019</option>`;

                if(value.year) {
                    optionYear = `<option value="${value.id}">${value.year}</option>`;
                }
                modelSelect.append(optionModel);
                yearSelect.append(optionYear)

            });
        }
        if(elemId === 'model') {
            let value = `<option value="${data.id}">${data.year}</option>`;
            yearSelect.empty();
            makeSelect.find(`option[value='${data.make.id}']`).attr(`selected`,`selected`);
            yearSelect.append(value);
        }
        if(elemId === 'year') {
            makeSelect.empty();
            modelSelect.empty();
            $.each(data.make,(key,value) => {
                let makeOption = `<option value="${value.make.id}">${value.make.name}</option>`;
                makeSelect.append(makeOption);
            });

            $.each(data.model,(key,value) => {
                let  modelOption = `<option value="${value.model.id}">${value.model.name}</option>`;
                modelSelect.append(modelOption);
            })
        }
    }

    generateView(data) {
       let cardBlock = $(`.card-columns`);
       if(cardBlock.has(`.card`))  cardBlock.empty();

       $.each(data,(key,value) => {
           let card = `<div class="card">
                            <img src="/images/300x200.png" alt="Part Image">
                            <div class="card-body">
                                <p class="card-text"><span class="font-weight-bold">EN</span>: ${value.en}</p>
                                <p class="card-text"><span class="font-weight-bold">ES</span>: ${value.es}</p>
                                 <p class="card-text"><span class=0"font-weight-bold">Part Number:</span> ${value.part}</p>
                                <a href="" class="btn btn-info">Show</a>
                            </div>
                        </div>`;
           $(`.card-columns`).append(card);
       });
    }

    reset() {
        let make = $(`#make`),
            model = $(`#model`),
            year = $(`#year`);
        make.val(``).find(`option[value='']`).attr(`selected`,`selected`);
        model.empty().append(`<option value=''>Model</option>`);
        year.empty().append(`<option value=''>Year</option>`);

        $.ajax({
            url : `/reset`,
            method : `post`,
            dataType : `json`,
        }).done(function(data) {
           $.each(data.models, (key,value) => {
               let option = `<option value="${value.id}">${value.name}</option>`;
               model.append(option);
           });

            $.each(data.years, (key,value) => {
                let option = `<option value="${value.id}">${value.year}</option>`;
                year.append(option);
            });
        });
    }
}

let request = new Request();

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).on(`submit`,`.formMake`,(e) => {
    e.preventDefault();
    let form = $(e.target)[0],
         url = $(e.target).find(`.request`).attr(`data-action`);
    if(url) request.getData(url,form);
});

$(document).on('change','select',(e) => {
    let elem = $(e.target),
        elemId = elem.attr(`id`),
        id = elem.val();
    request.fillSelect(elemId,id);
});

$(document).on(`click`,`.reset`,(e) => {
   request.reset();
});