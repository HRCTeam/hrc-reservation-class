# HRC Reservation PHP Class

## How to use
```
require_once('HRCReservation.php');
$error = false;

try {
    $reservation = new HRCReservation('demo.key');
   
    try {
        $reservation->send($_POST['date'], $_POST['time'], $_POST['name'], $_POST['phone'], $_POST['count'], $_POST['message']);
    } catch (Exception $exception) {
        $error = $exception->getMessage();
    }
} catch (Exception $exception) {
    $error = $exception->getMessage();
}
```