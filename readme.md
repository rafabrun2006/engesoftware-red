# Laravel Keycloak Guard

Esse projeto possui algumas classes responsáveis por adicionar o login via Keycloak nas aplicações Laravel.

# Instalação
Execute o seguinte comando
```
    composer config repositories.engesoftware composer https://satis.engesoftware.com.br
```
E execute o seguinte comando
```
    composer require engesoftware/keycloak-guard
```

Para que o laravel utilize o novo driver de autenticação e o novo user provider é necessário adicionar algumas linhas de código nos seguintes arquivos:

**config/auth.php** 
```php
return [
    // ...
    'guards' => [
        // ...
        'keycloak' => [
            'driver' => 'keycloak',
            'provider' => 'keycloak',
        ]
    ],
    'providers' => [
        // ...
        'keycloak' => [
            'driver' => 'keycloak'
        ]
    ],
    /*
    |--------------------------------------------------------------------------
    | Authentication Server
    |--------------------------------------------------------------------------
    |
    | Application server address for authentication
    |
    */
    'auth_server' => env('AUTH_SERVER', 'http://k01.engesoftware.com.br/auth/realms/JF1/protocol/openid-connect/userinfo'),

    /*
    |--------------------------------------------------------------------------
    | Authentication Project Key
    |--------------------------------------------------------------------------
    |
    | This is the key name of the project used to find user roles
    |
    */
    // MUDAR PARA A RESPECTIVA CHAVE DO SEU PROJETO ADVINDA DO KEYCLOAK - GERALMENTE É O NOME DO PROJETO. Exemplo: sicam
    'auth_roles_key' => env('AUTH_ROLES_KEY', 'laravel')
];
```

**app/Providers/AuthServiceProvider.php**
```php
class AuthServiceProvider extends ServiceProvider
{
    // ...
    
    // Definição dos alias das policies
    protected $policies = [
        'App\User' => 'App\Policies\UserPolicy'
    ];
    
    public function boot()
    {
        $this->registerPolicies();
        
        // Register the Keycloak User Provider
        Auth::provider('keycloak', function ($app, array $config) {
            return new \Engesoftware\Keycloak\Providers\User();
        });
        
        // Register Keycloak Guard
        Auth::extend('keycloak', function ($app, $name, array $config) {
            return new \Engesoftware\Keycloak\Guard(
                Auth::createUserProvider($config['provider']), 
                app('request'),
                config('auth.auth_server')
            );
        });
    }
}
```

**routes/api.php**
```php
// ...
Route::group(['middleware' => 'auth:keycloak'], function () {
    // ... definir suas rotas
});
```

**.env**
```php
AUTH_ROLES_KEY=CLIENT_NO_KEYCLOAK
AUTH_PROVIDER=keycloak
AUTH_DRIVER=keycloak
AUTH_SERVER=http://k01.engesoftware.com.br/auth/realms/JF1/protocol/openid-connect/userinfo
``` 
# Autorização

Para que seja possível autenticar uma action dentro de um controlador, são necessários alguns passos:

#### Criar uma Policy
```php
php artisan make:policy NOME_DA_POLICY
```

```php
namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
    
    /**
     * Policy for index action
     *
     * @param User $user
     * @param Request $request
     * @return bool
     */
    public function index(User $user, Request $request)
    {
        // Sua lógica de validação se o usuário pode acessar essa rota. Exemplo:
        return in_array('ROLE_USUARIO', $request->attributes->get("roles"));
    }
}
```

####  Chamar a função responsável por autorizar a rota
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExampleController extends Controller
{
    //
    public function index(Request $request)
    {
        // Essa função é responsável por validar a permissão do usuário na action index de acordo com as regras definidas na Policy App\User (alias definido no arquivo AuthServiceProvider)
        $this->authorize('index', ['App\User', $request]);

        //
        return ['message' => 'This is a private page. With authentication and authorization by the roles on JWT Token.', 'user' => Auth::user()];
    }
}
```