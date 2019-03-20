class Request
{
    constructor() {
        this.selected = [];
    }

    getData (url) {
        let input = $(`#name`),
            inpVal = input.val();

        if(!inpVal.trim() || inpVal.length > 100) {
            alert('Empty Data');
            input.val(``);
            return false;
        }

        $.ajax({
            url : url,
            data : {name : inpVal},

            type : "post",
            dataType : 'json',
            success : (data) =>
            {
                if(!data.length) {
                    alert('Empty');
                    input.val(``);
                }
                request.generateView(data);
            },
            error : () =>  {
                alert('This field is required');
            },
        });
    }

    fillSelect (elemId,id) {
        let make = $(`#make`).val(),
            model = $(`#model`).val(),
            year = $(`#year`).val();
        year = (year === 'Year') ? '' : year;

        if(id) {
            $.ajax({
                url : '/fill-select',
                method : 'post',
                data : {
                    make : make,
                    model : model,
                    year  :year,
                    selected : [this.selected[0],elemId,this.selected[this.selected.length-1]],
                },
                dataType : 'json',
            }).done(function(data) {
                request.generateSelect(data,elemId);
            });
        }
    }

    generateSelect(data,elemId) {
        let selected = this.selected,
            first = selected[0],
            last  = selected[selected.length - 1],
            count = selected.length;

        if(count === 1 || first === elemId) {
           let unique = this.unique(first),
               selectOne = unique[0],
               selectTwo = unique[1];

           this.emptySelect(selectOne,selectTwo);

           $.each(data[selectOne],(key,val) => {
              let selectBlock = `<option value="${val[selectOne + '_id']}">${val.name}</option>`;
              $(`#${selectOne}`).append(selectBlock);
           });

            $.each(data[selectTwo],(key,val) => {
                let selectBlock = `<option value="${(selectTwo === 'year') ? val.year : val[selectTwo+'_id']}">${val.name ? val.name :  val.year}</option>`;
                $(`#${selectTwo}`).append(selectBlock);
            });

        }else if(count === 2 || (first !== elemId && last !== elemId)) {
             let unique = this.unique(first,elemId),
                 select = unique[0];
             this.emptySelect(select);

             $.each(data[select], (key,val) => {
                 let selectBlock = `<option value="${(select === 'year') ? val.year : val[select+'_id']}">${val.name ? val.name :  val.year}</option>`;
                 $(`#${select}`).append(selectBlock);
             });
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
                                <p class="card-text"><span class="font-weight-bold">Part Number:</span> ${value.part}</p>
                                <p class="card-text"><span class="font-weight-bold">EN</span>: ${value.en}</p>
                                <p class="card-text"><span class="font-weight-bold">ES</span>: ${value.es}</p>                   
                                <a href="/show-part/${value.partId}" class="btn btn-info">Show</a>
                            </div>
                        </div>`;
           $(`.card-columns`).append(card);
       });
    }

    showSelected() {
        let makeVal = $(`#make`).val(),
            modelVal = $(`#model`).val(),
            year = $(`#year`).val(),
            yearVal = (year === 'Year') ? '' : year;

        if(!makeVal && !modelVal && !yearVal) {
            alert('Empty');
            return false;
        }

        let selected = {
            make : makeVal,
            model : modelVal,
            year  : yearVal
        };

        $.ajax({
            url : '/showSelected',
            method : 'post',
            dataType : 'json',
            data : {selected : selected},
        }).done((data) => {
            if(!data.length) {
                alert('Empty');
                return false;
            }
            this.generateTable(data);
        });
    }

    generateTable(data) {
        let table = $(`.showSelected`),
            paginateBlock = $(`.paginateBlock`);
        table.removeClass(`d-none`);
        paginateBlock.removeClass(`d-none`);

        $.each(data,(key,val) => {
            let row = `<tr>
                                <td>${key+1}</td>         
                                <td>${val.make[0].name}</td>         
                                <td>${val.model[0].name}</td>         
                                <td>${val.year}</td>         
                                <td><a href="/show-vehicle/${val.id}" class="btn btn-danger"><i class="far fa-eye mr-2"></i> Show</a></td>         
                           </tr>`;

            table.find(`tbody`).append(row);
        });
    }

    show(url,generate) {

       $.ajax({
           url : url,
           method : 'get',
           dataType : 'json',
       }).done((data) => {
           if(!data.length) {
               alert('Empty');
               return false;
           }
           request[generate](data);
       });
    }

    generatePart(data) {
        $.each(data,(key,value) => {
            let card = `<div class="card">
                            <img src="/images/300x200.png" alt="Part Image">
                            <div class="card-body">
                                 <p class="card-text"><span class="font-weight-bold">Part Number: </span> ${value.part}</p>   
                                <p class="card-text"><span class="font-weight-bold">EN: </span> ${value.en}</p>
                                <p class="card-text"><span class="font-weight-bold">ES: </span> ${value.es}</p>    
                                <a href="/show-part/${value.part_id}" class="btn btn-info">Show</a>                                   
                            </div>
                        </div>`;
            $(`.card-columns`).append(card);
        });
    }

    generateVehicle(data) {
        let table = $(`.showVehicle`);
        table.removeClass(`d-none`);

        $.each(data,(key,val) => {
            let row = `<tr>
                                <td>${key+1}</td>         
                                <td>${val.make}</td>         
                                <td>${val.model}</td>         
                                <td>${val.type_name}</td>         
                                <td>${val.year}</td>         
                                <td><a href="/show-vehicle/${val.id}" class="btn btn-danger"><i class="far fa-eye mr-2"></i> Show</a></td>         
                           </tr>`;

            table.find(`tbody`).append(row);
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
        $(`.card-columns`).empty();
        $(`#name`).val(``);
        showSelected.addClass(`d-none`);

        this.selected = [];

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
                let option = `<option value="${value.year}">${value.year}</option>`;
                year.append(option);
            });
        });
    }

    unique(firstSelect,lastSelect = '') {
        let selectNames = ['make','model','year'],
            finalArray = [];

        if(firstSelect && !lastSelect) finalArray = selectNames.filter((el) => (el !== firstSelect));
        else if(firstSelect && lastSelect) finalArray = selectNames.filter((el) => (el !== firstSelect && el !== lastSelect));

        return finalArray;
    }

      emptySelect(select1,select2 = '') {
        let _select1 = select1.charAt(0).toUpperCase() + select1.slice(1);
        let defaultVal = `<option value="">${_select1}</option>`;
        $(`#${select1}`).empty().append(defaultVal);

        if(select2) {
            let _select2 = select2.charAt(0).toUpperCase() + select2.slice(1);
            defaultVal = `<option value="">${_select2}</option>`;
            $(`#${select2}`).empty().append(defaultVal);
        }

        return true;
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
    let url = $(e.target).find(`.request`).attr(`data-action`);
    if(url) request.getData(url);
});

$(document).on('change','select',(e) => {
    let elem = $(e.target),
        elemId = elem.attr(`id`),
        id = elem.val(),
        selectArr = request.selected;

        if(selectArr.length === 0 || $.inArray(elemId,selectArr) === -1) {
            selectArr.push(elemId);
        }
    request.fillSelect(elemId,id);
});

$(document).on(`submit`,`#selectForm`,(e) => {
    e.preventDefault();
    $(`.showSelected tbody tr`).empty();
    $(`.card-columns`).empty();
    request.showSelected();
});

$(document).on(`click`,`.showParts`,(e) => {
    e.preventDefault();
    let elem = $(e.target),
        url = elem.attr(`data-action`),
        showFunc = elem.attr(`data-func`);
    elem.attr(`disabled`,`disabled`);
    $(`.card-columns`).empty();
    request.show(url,showFunc);
});

$(document).on(`click`,`.reset`,(e) => {
    e.preventDefault();
    request.reset();
});