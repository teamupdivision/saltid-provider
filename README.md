<h1 align="center">Provider auth SaltId</h1>

<p align="center">

## How to use package

### 1. Put in config/services.php :
```
'saltid' => [
    'client_id' => env('SALTID_CLIENT_ID'),
    'client_secret' => env('SALTID_CLIENT_SECRET'),
    'redirect' => env('SALTID_REDIRECT'),
    'url' => env('SALTID_URL'),
],
```
### 2. Put credentials in your .env file:
```
SALTID_CLIENT_ID=yourclient-key-from-saltid
SALTID_CLIENT_SECRET=your-secret-key-from-saltid
SALTID_REDIRECT=http://your-domain.com/salt/callback
SALTID_URL=http://saltid.com/
```
<b>Obs.</b> <i>These are generated from SaltId User Management</i>

### 3. Create routes:
```
Route::get('salt/redirect',  [SaltController::class, 'redirect']);
Route::get('salt/callback', [SaltController::class, 'callback']);
```
### 4. Create your controller that manages sso auth with SaltId with functions:
 - run `composer require teamupdivision/saltid-provider`
 - import package :
   - `use Teamupdivision\SaltId\Facades\SaltId;`
 - create `redirect` function:
```
    /**
     * Redirect function to external call for authorization step
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function redirect(Request $request): RedirectResponse
    {
        $redirect = SaltId::driver('saltid')->redirect();
        return $redirect;
    }
```
- create `callback` function:
```
    /**
     * Callback function for authorization and get user
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function callback(Request $request): RedirectResponse
    {
        $saltUser = SaltId::driver('saltid')->user();
        $user = User::where('email',$saltUser->getEmail())->first();

        if(!$user){
            $user = new User;
            $user->name = $saltUser->getName() ? $saltUser->getName() : $saltUser->getEmail();
            $user->email = $saltUser->getEmail();
            $user->password = bcrypt(123456);
            $user->save();
        }

        Auth::login($user);

        return redirect('/dashboard');
    }
```

## How to use in manual mode:
### 1. Please follow steps (1) (2) (3) from above

### 2. Create your controller that manages sso auth with SaltId with functions:
 - create `redirect` function:
```
    /**
     * Redirect function to external call for authorization step
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function redirect(Request $request): RedirectResponse
    {
       $request->session()->put('state', $state = Str ::random(40));

        $request->session()->put(
            'code_verifier', $code_verifier = Str::random(128)
        );

        $codeChallenge = strtr(rtrim(
            base64_encode(hash('sha256', $code_verifier, true))
        , '='), '+/', '-_');

        $query = http_build_query([
            'client_id' =>  config('services.saltid.client_id'),
            'redirect_uri' => config('services.saltid.redirect'),
            'response_type' => 'code',
            'state' => $state,
        ]);

        return redirect(config('services.saltid.url').'oauth/authorize?'.$query);
    }
```
- create `callback` function:
```
    /**
     * Callback function for authorization and get user
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function callback(Request $request): RedirectResponse
    {
        $state = $request->session()->pull('state');
        $codeVerifier = $request->session()->pull('code_verifier');

        throw_unless(
            strlen($state) > 0 && $state === $request->state,
            InvalidArgumentException::class
        );

        $response = Http::asForm()->post(config('services.saltid.url').'oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.saltid.client_id'),
            'client_secret' => config('services.saltid.client_secret'),
            'redirect_uri' => config('services.saltid.redirect'),
            'code_verifier' => $codeVerifier,
            'code' => $request->code,
        ]);

        if($response->failed()) {
            $errorMessage = $response->body();
            return redirect('/login')->with('error',str_replace('"', '', $errorMessage));
        }


        $saltUser = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$response->json()['access_token']
        ])->get(config('services.saltid.url').'api/v1/me');

        $user = User::where('email',$saltUser->json()['data']['email'])->first();

        if(!$user){
            $user = new User;
            $user->name = $saltUser->json()['data']['email'];
            $user->email = $saltUser->json()['data']['email'];
            $user->password = bcrypt(123456);
            $user->save();
        }

        Auth::login($user);

        return redirect('/dashboard');
    }
```
