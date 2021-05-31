function toggleMedia(){
    document.querySelector('#hide-mini-image').classList.toggle('hidden');
    document.querySelector('#hide-mini-video').classList.toggle('hidden');
}

$(document).ready(function () {
    let page = 1;
    let total = $('#load-more').data('total');

    $('#load-more').on('click', function (e) {
        e.preventDefault()

        if (page < total) {
            page++

            $.ajax({
                type:'get',
                url: '/comments/'+$(this).data('id')+'/'+page,
                success: function (data) {
                    $('#comments').append(data)
                    if(page >= total) {
                        $('#load-more').hide()
                    }
                }
            })
        } else {
            $(this).hide()
        }
    })
})