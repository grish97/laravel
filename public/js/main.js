class Request
{
    constructor() {
        this.make = false;
        this.model = false;
        this.year = false;
    }

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
            this.make = true;
            modelSelect.empty();
            yearSelect.empty();

            $.each(data.models,(key,value) => {
                let optionModel = `<option value="${value.model.id}">${value.model.name}</option>`;
                modelSelect.append(optionModel);
            });

            $.each(data.years,(key,value) => {
                let  optionYear = `<option value="${value.id}">${value.year}</option>`;
                yearSelect.append(optionYear);
            });
        }
        if(elemId === 'model') {
            this.model = true;
            yearSelect.empty();
            makeSelect.empty();

            $.each(data,(key,value) => {
                let makeOption = `<option value="${value.make.id}">${value.make.name}</option>`,
                    yearOption = `<option value="${value.id}">${value.year}</option>`;
                yearSelect.append(yearOption);
                makeSelect.append(makeOption);
            });
        }
        if(elemId === 'year') {
            this.year = true;
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
        $(`.card-columns`).empty();
        $(`.showSelected`).addClass(`d-none`);
        $(`.showSelected tbody tr`).empty();

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

    showSelected() {
        let make = this.make,
            model = this.model,
            year = this.year,
            formData = null;

        let makeSelect = $(`#make`),
            modelSelect = $(`#model`),
            yearSelect = $(`#year`);

        if(make && !model && !year) {
            formData = {make : makeSelect.val()};
        }else if((make && model && !year) || (!make && model && !year) || (!make && model && year)) {
            formData = {model : modelSelect.val()};
        }else if(!make && !model && year) {
            formData = {year : yearSelect.val()};
        }

            $.ajax({
               url : `/showSelected`,
               type : `post`,
               dataType : `json`,
               data : {formData : formData},
            }).done(function(data) {
                let showSelected = $(`.showSelected`);
                showSelected.removeClass(`d-none`);

                if($.isArray(data)) {
                    $.each(data, (key,value) => {
                        let block = `<tr>
                                          <th scope="row">${key+1}</th>
                                          <td>${value.make.name}</td>
                                          <td>${value.model.name}</td>
                                          <td>${value.year}</td>
                                          <td><a href="show?" class="btn btn-danger"><i class="far fa-eye mr-2"></i> Show</a></td>
                                      </tr>`;
                        $(`.showSelected tbody`).append(block);
                    })
                }

            });
    }

    reset() {
        let make = $(`#make`),
            model = $(`#model`),
            year = $(`#year`),
            showSelected = $(`.showSelected`);

        make.empty().append(`<option value=''>Make</option>`);
        model.empty().append(`<option value=''>Model</option>`);
        year.empty().append(`<option value=''>Year</option>`);

        showSelected.find(`tbody tr`).empty();
        showSelected.addClass(`d-none`);

        this.make = false;
        this.model = false;
        this.year = false;

        $.ajax({
            url : `/reset`,
            method : `post`,
            dataType : `json`,
        }).done(function(data) {
           $.each(data.makes,(key,value) => {
               let option = `<option value="${value.id}">${value.name}</option>`;
               make.append(option);
           });

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
    e.preventDefault();
   request.reset();
});
$(document).on(`submit`,`#selectForm`,(e) => {
    e.preventDefault();
    $(`.showSelected tbody tr`).empty();
    $(`.card-columns`).empty();
    request.showSelected();
});