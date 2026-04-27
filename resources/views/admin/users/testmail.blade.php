use App\Mail\UserRegistered;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

Route::get('/test-correo', function () {
    $user = new User();
    $user->name = "Usuario de prueba";
    $user->email = "JonathanSM117@hotmail.com"; // Cambia por tu correo

    Mail::to($user->email)->send(new UserRegistered($user));

    return "Correo enviado correctamente.";
});
