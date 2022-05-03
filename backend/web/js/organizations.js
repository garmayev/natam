$(() => {
    $("#client-company").on("keyup", (e) => {
        let target = $(e.currentTarget);
        $.ajax({
            url: 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/party',
            headers: {
                Authorization: "Token 2c9418f4fdb909e7469087c681aac4dd7eca158c",
                Accept: "application/json",
                "Content-Type": "application/json",
            },
            type: "POST",
            data: JSON.stringify({
                query: $(e.currentTarget).val(),
                location: "03",
                // type: "LEGAL",
                status: "ACTIVE"
            }),
            success: (response) => {
                let args = [];
                for (let i = 0; i < response.suggestions.length; i++) {
                    let item = response.suggestions[i];
                    args.push(`${item.value} (${item.data.inn})`)
                    // console.log(item)
                }
                console.log(response);
                target.autocomplete({
                    source: args
                })
            }
        })
    })
});