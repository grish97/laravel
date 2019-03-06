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
        if(elemId === 'make') {
            $(`#model`).empty();
            $(`#year`).empty();
            $.each(data,(key,value) => {
                let optionModel = `<option value="${value.id}">${value.name}</option>`;
                $(`#model`).append(optionModel);
                if(value.vehicle) {
                    let optionYear = `<option value="${value.vehicle.id}">${value.vehicle.year}</option>`;
                    $(`#year`).append(optionYear)
                }
            });
        }
        if(elemId === 'model') {
            if(data.makeYear) {
                console.log(data.makeYear.year);
                let option = `<option value="${data.makeYear.id}">${data.makeYear.year}</option>`;
                $(`#year`).empty();
                $(`#year`).append(option);
            }

            $(`#make`).find(`option[value=${data.make.id}]`).attr(`selected`,`selected`);

        }
        if(elemId === 'year') {
            $(`#year`).empty();
            $.each(data,(key,value) => {
                let option = `<option value="${value.id}">${value.name}</option>`;
                $(`#make`).append(option);
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
                                 <p class="card-text"><span class="font-weight-bold">Part Number:</span> ${value.part}</p>
                                <a href="" class="btn btn-info">Show</a>
                            </div>
                        </div>`;
           $(`.card-columns`).append(card);
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