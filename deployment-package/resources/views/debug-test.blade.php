<!DOCTYPE html>
<html>
<head>
    <title>Debug Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Debug Test Page</h1>
    
    <script>
        console.log('jQuery loaded:', typeof $ !== 'undefined');
        console.log('CSRF token:', $('meta[name="csrf-token"]').attr('content'));
        
        // Test AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        console.log('AJAX setup complete');
        
        // Test form submission
        function testFormSubmission() {
            console.log('Testing form submission...');
            
            $.post('{{ route("cash-books.store-simple") }}', {
                entry_date: '{{ date("Y-m-d") }}',
                transaction_type: 'receive',
                account_id: 1,
                vehicle_id: 1,
                description: 'Test from debug page',
                amount: 50
            })
            .done(function(response) {
                console.log('Success:', response);
            })
            .fail(function(xhr) {
                console.log('Error:', xhr.responseText);
            });
        }
        
        setTimeout(testFormSubmission, 1000);
    </script>
</body>
</html>