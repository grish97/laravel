class Request
{
    getData (url,form) {
        let formData = new FormData(form);

        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
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

    generateView(data) {
       if(data.desc) {
           $.each(data.desc,(key,value) => {
               console.log(value.part);
               let card = `<div class="card">
                                <img src="/images/300x200.png" alt="Part Image">
                                <div class="card-body">                                  
                                    <p class="card-text"><span class="font-weight-bold">EN</span>: ${value.en}</p>
                                    <p class="card-text"><span class="font-weight-bold">ES</span>: ${value.es}</p>
                                     <p class="card-text"><span class="font-weight-bold">Part Number:</span> ${value.part.part}</p>
                                    <a href="" class="btn btn-info">Show</a>
                                </div>
                            </div>`;
               $(`.card-columns`).append(card);
           });
       }else {
           $.each(data.parts,(key,value) => {
               console.log(value.part);
               let card = `<div class="card">
                                <img src="/images/300x200.png" alt="Part Image">
                                <div class="card-body">                                    
                                    <p class="card-text"><span class="font-weight-bold">EN</span>: ${value.description.en}</p>
                                    <p class="card-text"><span class="font-weight-bold">ES</span>: ${value.description.es}</p>
                                    <p class="card-text"><span class="font-weight-bold">Part Number: </span> ${value.part}</p>
                                    <a href="" class="btn btn-info">Show</a>
                                </div>
                            </div>`;
               $(`.card-columns`).append(card);
           });
       }
    }
}

let request = new Request();

$(document).on(`submit`,`.formMake`,(e) => {
    e.preventDefault();
    let form = $(e.target)[0],
         url = $(e.target).find(`.request`).attr(`data-action`);
    if(url) request.getData(url,form);
});

$(document).on(`click`,`.clear`,(e) => {
    $(`#view`).empty();
});