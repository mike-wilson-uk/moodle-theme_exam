<?php
//Set date as today or get it from form submission
            $today = date('d/m/Y',time());
            if (!isset($_GET['date'])){
		$date = $today;
            }
            if(isset($_GET['date'])){
                $date = $_GET['date'];
            }
?>
<!DOCTYPE html>
    <html>
        <head>
            <title>TODO supply a title</title>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            
            <script src='https://code.jquery.com/jquery-1.9.1.min.js'></script>
            <script src='dist/js/bootstrap-datepicker.min.js' type='text/javascript'></script>
                       
            <link id='bs-css' href='https://netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css' rel='stylesheet'>
            <link href='dist/css/bootstrap-datepicker3.min.css' rel='stylesheet' type='text/css'/>
        </head>
        <body>
            
            <form action='index.php' method='get' id='date-selector'>
                <div class='input-group date'>
                    <input type='text' class='form-control' name='date' id='date' value='<?php echo $date; ?>'>
                    <span class='input-group-addon'>
                    <i class='glyphicon glyphicon-th'></i></span>
                </div>
            </form>
            <script>
                $('#date-selector .input-group.date').datepicker({
                    format: 'dd/mm/yyyy',
                    daysOfWeekHighlighted: '1,2,3,4,5',
                    autoclose: true
                });
                
                $('#date').change(function() {
                    this.form.submit();
                 });
            </script>
        </body>
    </html>
