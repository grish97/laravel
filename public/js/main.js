$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

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
            success : (data, status) => {
                let makes = data.makes;
                request.generateView(makes);
            },
            error : (data, status) =>  {
                let elem = `<h3 class="text-center">Empty</h3>`;
                $(`#view`).append(elem);
                return false;
            },
        });
    }

    generateView(data) {
        console.log(data);
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