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
                console.log(data);
                request.generateView(data);
            },
            error : (data) =>  {
                console.error(data.responseText)
            },
        });
    }

    generateView(data) {
        $(`.table`).removeClass(`d-none`);

        $.each(data, (key, value) => {
            console.log(value);
            let tr  = ` <tr>
                        <th class="row">${key}</th>
                        <td>${value.name}</td>
                        <td>${(value.model[key])}</td>
                        </tr>`;
            $(`tbody`).append(tr);
        })
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