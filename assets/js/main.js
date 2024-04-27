jQuery(function(){
    
    MicroModal.init({
        onShow: modal => console.info(`${modal.id} is shown`),
        onClose: modal => {
            let formType1 = $('.check-type-1-js');
            let formType2 = $('.check-type-2-js');
            let formTypeButtons = $('.type-buttons-js');

            if( $(formType1).length ) $(formType1).hide();
            if( $(formType2).length ) $(formType2).hide();
            if( $(formTypeButtons).length ) $(formTypeButtons).show();
        },
        openTrigger: 'data-custom-open',
        closeTrigger: 'data-custom-close',
        openClass: 'is-open',
        disableScroll: true,
        disableFocus: false,
        awaitOpenAnimation: false,
        awaitCloseAnimation: false,
        debugMode: true
    });


    $( ".add-check-form-js" ).on( "submit", function( event ) {
        const form = event.target;

        let date = $(form).find("input[name='date']").val();
        let time = $(form).find("input[name='time']").val();
        let price = $(form).find("input[name='price']").val();
        let type = $(form).find("select[name='type']").children("option:selected").val();
        let type_comment = $(form).find("input[name='type_comment']").val();

        
        $.ajax({
            url: '/ajax/add-check.php',
            type: "GET",
            data: {
                DATE: date,
                TIME: time,
                PRICE: price,
                TYPE: type,
                TYPE_COMMENT: type_comment,
            },
            success: function( data ){
                if( data === true ) window.location.reload();
            }
        });

        event.preventDefault();
    });

    const xlsxInput = $(".add-xlsx-form-js input[type='file']");
    $( xlsxInput ).on( "change", function( event ) {

        let file = event.target.files[0];
        let formData = new FormData();
        formData.append('file', file);

        $.ajax({
            url: '/ajax/checked-xlsx.php',
            type: "POST",
            data: formData,
            contentType: false,
            processData:false,
            success: function( data ){

                if( data.result ){
                    $('.table-props-js').prepend( data.html );
                    $('.file-label-js').hide();
                    $('.table-props-js').show();
                } else {
                    $(xlsxInput).val('');
                }

            }
        });
    });

    $( ".add-xlsx-form-js" ).on( "submit", function( event ) {

        let file = xlsxInput[0].files[0];
        let col_id = $("select[name='col_id']").children("option:selected").val();
        let col_date = $("select[name='col_date']").children("option:selected").val();
        let col_price = $("select[name='col_price']").children("option:selected").val();

        if( col_id && col_date && col_price ){


            let formData = new FormData();
            formData.append('file', file);
            formData.append('COL_ID', col_id);
            formData.append('COL_DATE', col_date);
            formData.append('COL_PRICE', col_price);

            $.ajax({
                url: '/ajax/save-xlsx.php',
                type: "POST",
                data: formData,
                contentType: false,
                processData:false,
                success: function( data ){
                    if( data.result === true ) window.location.reload();
                }
            });
        }
        
        event.preventDefault();
    });


    const qrInput = $(".add-check-form-js input[type='file']");
    $( qrInput ).on( "change", function( event ) {
        let file = qrInput[0].files[0];
        let formData = new FormData();
            formData.append('file', file);

        if( file ){
            $.ajax({
                url: 'https://api.qrserver.com/v1/read-qr-code/',
                type: "POST",
                data: formData,
                contentType: false,
                processData:false,
                success: function( data ){
                    
                    const result = data[0].symbol[0];
                    console.log(result);

                    if( !result.error ){
                        let searchParams = new URLSearchParams(result.data);
                        let date = searchParams.get('t');
                        let price = searchParams.get('s');

                        if( date && price ){

                            $(".qr-props-js input[name='date']").val(moment(date).format('yyyy-MM-D'));
                            $(".qr-props-js input[name='time']").val(moment(date).format('HH:mm'));
                            $(".qr-props-js input[name='price']").val(price);

                            $('.qr-label-js').hide();
                            $('.qr-props-js').show();

                        } else {
                            alert('QR-код не является чеком');    
                        }
                    } else {
                        alert('Не удалось распознать QR-код');
                    }
                }
            });
        }
    });


    $('.type-buttons-js button').on( "click", function( event ) {
        
        const blockId = $(event.target).data('id');

        if( blockId === 1 ){

            $('.check-type-2-js').hide();
            $('.check-type-1-js').show();

        } else if( blockId === 2 ) {

            $('.check-type-1-js').hide();
            $('.check-type-2-js').show();

        }

        $('.type-buttons-js').hide();
        
    });
});