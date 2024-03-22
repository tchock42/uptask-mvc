<?php
namespace Model;

class Usuario extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    //validar el login de Usuarios
    public function validarLogin(){
        if(!$this->email){ //revisa que el email no esté vacío
            self::$alertas['error'][] = 'El Email del Usuario es obligatorio';   
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){ //revisa que el email sea válido
            self::$alertas['error'][] = 'El Correo no es valido';
        }
        if(!$this->password){ //revisa que la contraseña no esté vacío
            self::$alertas['error'][] = 'La contraseña de la cuenta es obligatoria';   
        }

        return self::$alertas;
    }
    //Validacion para cuentas nuevas
    public function validarNuevaCuenta(){

        if(!$this->nombre){
             self::$alertas['error'][] = 'El Nombre del Usuario es obligatorio';   
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El Email del Usuario es obligatorio';   
        }
        if(!$this->password){
            self::$alertas['error'][] = 'La contraseña de la cuenta es obligatoria';   
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 caracteres';   
        }
        if($this->password !== $this->password2){
            self::$alertas['error'][] = 'Las constraseñas deben coincidir';   
        }
        return self::$alertas;
    }
    //valida un email
    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = 'El Correo es obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'El Correo no es valido';
        }
        return self::$alertas;
    }

    public function nuevo_password(): array{
        if(!$this->password_actual){
            self::$alertas['error'][] ='La contraseña actual no puede ir vacío';
        }
        if(!$this->password_nuevo){
            self::$alertas['error'][] ='La contraseña nueva no puede ir vacío';
        }
        if(strlen($this->password_nuevo) < 6){
            self::$alertas['error'][] ='La nueva contraseña debe tener al menos 6 caracteres';
        }
        return self::$alertas;
    }
    //comprobar la contraseña nueva
    public function comprobar_password(): bool{ //verifica la contraseña
        return password_verify($this->password_actual, $this->password);
    }
    //hashea la contraseñña
    public function hashPassword(): void{
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }
    //generar un token
    public function crearToken(): void{
        $this->token = uniqid();
    }

    public function validarPassword(){
        if(!$this->password){
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        }
        if($this->password < 6){
            self::$alertas['error'][] = 'La contraseña debe tener mas de 6 carateres';
        }
        return self::$alertas;
    }
    public function validar_perfil(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre es obligtorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El correo es obligtorio';
        }
        return self::$alertas;
    }
}