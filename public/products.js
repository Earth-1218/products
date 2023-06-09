
    var clms = [];
    let table = {};
    let atLeastOneRequired;
    $(document).ready(function(){
        atLeastOneRequired = 0;
        $('#clear').hide();
        executeFormValidation();
        initPref();
    });
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    function executeFormValidation() {
        'use strict'
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            saveproduct();
            form.classList.add('was-validated')
            }, false)
        })
    }

    function editproduct(id)
    {   
        let pfd ={
            '_token' : token ,
        }
    
        $.ajax({
            url: apiProducts+"/"+id,
            type:"get",
            data:pfd,
            dataType:"json",
            success: function(response){
                    console.log(response);
                    var product = response.data;
                    var sku = '';
                    var status = '';
                    $('#saveSection').html(`
                        <button type="button" onclick="saveproduct(${id})" class="btn btn-primary">Save</button>
                    `)
                    if(product)
                    {
                        var form = document.getElementById("product_form");
                        form.reset
                        $('#name').val(product.name);
                        $('#price').val(product.price);
                        $('#sku').val(product.sku);
                        $('#details').val(product.details);
                        $('#status').val(product.status);
                    }
                    $('#productsModal').modal('show');
                },
                error: function(xhr, error){
                console.log(error);
            }
        })
    }

    function createproduct()
    {
        $('#name').val('');
        $('#price').val('');
        $('#sku').val('');
        $('#details').val('');
        $('#status').val('');
        $('#productsModal').modal('show');
    }

    function saveproduct(id = null) {
        let url = '';
        let fd = {};
        
        if (id !== null) {
            url = apiProducts + '/' + id;
        } else {
            url = apiProducts;
        }
    
        fd = {
            '_token': token,
            "name": $('#name').val(),
            "price": $('#price').val(),
            "details": $('#details').val(),
            "sku": $('#sku').val(),
            "status": $('#status').val()
        };
    
        
        $.ajax({
            url: url,
            type: (id != null) ? 'PUT' : 'POST',
            data: fd,
            dataType: "json",
            success: function(response) {
                if (response.status === true) {
                    getProductList();
                    $('#productsModal').modal('hide');
                }
            },
            error: function(xhr, error) {
                console.log(error);
            }
        });
    }
    
    var searchButtonClicked = false; // Flag variable

    function getProductList(page = 1, columns = [
        { name: 'is_checked', data: null, orderable: false, searchable: false },
        { name: 'id', data: 'id', orderable: true, searchable: false },
        { name: 'name', data: 'name', orderable: true, searchable: true },
        { name: 'details', data: 'details', orderable: true, searchable: true },
        { name: 'sku', data: 'sku', orderable: true, searchable: true },
        { name: 'price', data: 'price', orderable: true, searchable: true },
        { name: 'status', data: 'status', orderable: true, searchable: true },
        { name: 'action', data: null, orderable: false, searchable: false }
    ]) {
        var table;
    
        function initializeTable() {
            table = $('#product_table').DataTable({
                order: [[0, 'desc']],
                autoWidth: false,
                destroy: true,
                processing: true,
                serverSide: true,
                paging: true,
                bFilter: true,
                ordering: true,
                searching: false,
                // aaSorting: [[2, "asc"][3, "asc"],[4, "asc"]],
                ajax: {
                    url: apiProducts,
                    type: "GET",
                    data: function (d) {
                        if (searchButtonClicked) {
                            d.page = page;
                            d.name = $('#name-input').val();
                            d.sku = $('#sku-input').val();
                            d.details = $('#details-input').val();
                            d.price = $('#price-input').val();
                            d._token = token;
                        }
                    },
                    dataType: "json",
                    error: function (xhr, error) {
                        console.log(error);
                    }
                },
                columns: columns,
                columnDefs: [
                    {
                        defaultContent: "-",
                        targets: "_all",
                        targets: 0,
                        render: function (data, type, row) {
                            return `<div class="prochklist"> <input class="checkbox prochk product_selector"  type="checkbox" value="${row.id}" data-id="${row.id}" /></span> </div>`
                        }
                    },
                    {
                        targets: 7,
                        render: function (data, type, row) {
                            return `<div class="d-flex">
                                <button class="btn btn-primary" onclick="editproduct(${row.id})"><i class="fa fa-edit"></i></button>
                                &nbsp;
                                <button class="btn btn-danger" onclick="deleteproduct(${row.id})"><i class="fa fa-trash"></i></button>
                            </div>`;
                        }
                    }
                ],
    
                initComplete: function () {
                    // Create custom search filters for each column
                    $('#btn-search').on('click', function () {
                        searchButtonClicked = true;
                        setColumns();
                        reloadTable();
                    });
    
                    $('#product_table_processing').hide();
                    setColumns();
                }
            });
        }
    
        function setColumns() {
            // Show/hide columns based on filter checkboxes
            if (!$('#name-filter').prop('checked'))
                table.column(1).visible(false);
    
            if (!$('#details-filter').prop('checked'))
                table.column(2).visible(false);
    
            if (!$('#sku-filter').prop('checked'))
                table.column(3).visible(false);
    
            if (!$('#price-filter').prop('checked'))
                table.column(4).visible(false);
    
            if (!$('#status-filter').prop('checked'))
                table.column(5).visible(false);
        }
    
        function reloadTable() {
            table.ajax.reload(null, false); // Reload the table without resetting the current page
            searchButtonClicked = false; // Reset the flag variable
        }
    
        initializeTable();
    }
    

    function deleteproduct(id)
    {
        ConfirmDialog(apiProducts,id,'Are you sure you want to delete product ?');
    }

    function isNumericKey(event) 
    {
        const keyCode = event.which ? event.which : event.keyCode;
        // Check if the key code corresponds to a numeric or decimal key
        return (
            (keyCode >= 48 && keyCode <= 57) || // Numeric keys (0-9)
            keyCode === 46 || // Decimal key (.)
            keyCode === 8 || // Backspace key
            keyCode === 9 || // Tab key
            keyCode === 13 || // Enter key
            keyCode === 37 || // Left arrow key
            keyCode === 39 || // Right arrow key
            keyCode === 190 || // Period (.) on numpad
            keyCode === 110 // Decimal point (.) on numpad
        );
    }

    function getColumns() {
        var fd = {
            'name': ($('#name-filter').prop('checked')) ? 1 : 0,
            'sku': ($('#sku-filter').prop('checked')) ? 1 : 0,
            'details': ($('#details-filter').prop('checked')) ? 1 : 0,
            'price': ($('#price-filter').prop('checked')) ? 1 : 0,
            'status': ($('#status-filter').prop('checked')) ? 1 : 0
        };
        return fd;
    }

    function initPref()
    {
        $.ajax({
            url: apiPreferences,// 
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                
                console.log(response); // Print the response to the console for testing
                // Access the preferences data
                var preferences = response.data;

                if(preferences.name){
                    $('#name-filter').attr('checked','checked');
                }

                if(preferences.sku){
                    $('#sku-filter').attr('checked','checked');
                }

                if(preferences.details){
                    $('#details-filter').attr('checked','checked');
                }

                if(preferences.price){
                    $('#price-filter').attr('checked','checked');
                }

                if(preferences.status){
                    $('#status-filter').attr('checked','checked');
                }
                getProductList();
            },
            error: function(xhr, status, error) {

                console.error(error);
            }
        });
    }

    function putPref(){
            $.ajax({
            url: apiPreferences, // Replace with the actual URL of your API endpoint
            type: 'PUT',
                data:{
                '_token': token,   
                'name': ($('#name-filter').prop('checked')) ? 1 : 0,
                'sku': ($('#sku-filter').prop('checked')) ? 1 : 0,
                'details': ($('#details-filter').prop('checked')) ? 1 : 0,
                'price': ($('#price-filter').prop('checked')) ? 1 : 0,
                'status': ($('#status-filter').prop('checked')) ? 1 : 0
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
            }
        });
    }

    function getPref(){
        var pref = {};
            $.ajax({
            url: apiPreferences, // Replace with the actual URL of your API endpoint
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                pref = response.data;
                console.log(response);
            }
        });
        return pref;
    }

    function exportVisibleRowsToCSV(tableId) {
        var table = document.getElementById(tableId);
        var rows = table.getElementsByTagName('tr');
        var csvContent = '';

        // Extract table headings
        var headerRow = rows[0];
        var headerCells = headerRow.getElementsByTagName('th');
        var headerRowContent = '';

        for (var k = 0; k < headerCells.length; k++) {
            var headerCell = headerCells[k];
            headerRowContent += headerCell.textContent.trim() + ',';
        }

        headerRowContent = headerRowContent.slice(0, -1); // Remove the trailing comma
        csvContent += headerRowContent + '\r\n';

            // Extract visible rows
            for (var i = 1; i < rows.length; i++) {
                var row = rows[i];

                if (!isHidden(row)) {
                    var cells = row.getElementsByTagName('td');
                    var csvRow = '';

                    for (var j = 0; j < cells.length; j++) {
                        var cell = cells[j];
                        csvRow += cell.textContent.trim() + ',';
                    }

                    csvRow = csvRow.slice(0, -1); // Remove the trailing comma
                    csvContent += csvRow + '\r\n';
                }
            }

            downloadCSV(csvContent);
    }

    function isHidden(element) {
        return element.offsetParent === null;
    }

    function downloadCSV(content) {
        var encodedUri = 'data:text/csv;charset=utf-8,' + encodeURIComponent(content);
        var link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', 'table_data.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // $('#product_form').on('submit',function(){
    //     saveproduct();
    // });

    $('#preferences-save').on('click',function(){
        putPref();
        getProductList();
    });

    $('#btn-clear-search').on('click',function(){
        $('#name-input').val('');
        $('#sku-input').val('');
        $('#details-input').val('');
        $('#price-input').val('');
        getProductList();
    });

    $('#preferences-reset').on('click',function(){
        $('#name-filter').prop('checked','checked');
        $('#details-filter').prop('checked','checked');
        $('#sku-filter').prop('checked','checked');
        $('#price-filter').prop('checked','checked');
        $('#status-filter').prop('checked','checked');
        putPref();
        getProductList();
    });

    $('.btn-dropdown1').on('click',function(){
        $('.dropdown-content1').toggle();
    });

    $('.btn-dropdown2').on('click',function(){
        $('.dropdown-content2').toggle();
    });

    $('#btn-close-search').on('click',function(){
        $('.dropdown-content1').hide();
    });

    $('#preferences-close').on('click',function(){
        $('.dropdown-content2').hide();
    });

    function ConfirmDialog(api, deleteId, message)
    {
        Swal.fire({
            title: 'Delete',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Delete'
          }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: api+"/"+deleteId,
                    type:"delete",
                    data:{'_token' : token },
                    dataType:"json",
                    success: function(response){
                            getProductList();
                    },
                    error: function(xhr, error){
                            console.log(error);
                    }
                });
            }
        })
    }

    function truncate(){
        Swal.fire({
            title: 'Truncate',
            text: 'Are you sure you want to delete whole data ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Delete'
        }).then((result) => { 
            if (result.isConfirmed) {
            $.ajax({
                    url: apiProducts+'/truncate',
                    type:'POST',
                    data: { '_token' : token},
                    dataType: "json",
                    success: function(response) {
                        getProductList();
                    },
                    error: function(xhr, error) {
                        console.log(error);
                    }
                });
            }
        });
    }

    $('.selall').on('change',function(){
        if($(this).is(':checked')){
            $('.prochklist').find('input[type=checkbox]').prop('checked',true);
        }else{
            $('.prochklist').find('input[type=checkbox]').prop('checked',false);
        }
    });



    function deleteSelectedProducts(){
        Swal.fire({
            title: 'Delete Selected',
            text: 'Are you sure you want to delete selected data ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Delete'
        }).then((result) => { 
            if (result.isConfirmed) {
                let selectedProducts = [];
                $('.checkbox').each(function (index, obj) {
                    if (this.checked === true) {
                        selectedProducts[index] = obj.value; 
                    }
                });
                if(selectedProducts.length > 0)
                {
                    $.ajax({
                        url: apiProducts+'/delete/selected',
                        type:'POST',
                        data: { '_token' : token, 'selected_product_ids' : selectedProducts },
                        dataType: "json",
                        success: function(response) {
                            if(response.status){
                                getProductList();
                            }
                        },
                        error: function(xhr, error) {
                            console.log(error);
                        }
                    });
                }
            }
        });
    }


    $('.checkbox').on('click',function(){
        if($(this).is(':checked')){

        }
    });

    $(document).on('change', '.product_selector', function() {
        var isChecked = $('.product_selector:checked').length > 0;
            if(isChecked){
                $('#clear').show();
            }else{
                $('#clear').hide(); 
            }
    });
   
    
    

    
    





   