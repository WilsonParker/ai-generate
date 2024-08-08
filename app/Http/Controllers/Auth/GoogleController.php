<?php

namespace App\Http\Controllers\Auth;

use App\Events\Brevo\UserJoinEvent;
use App\Http\Controllers\BaseController;
use App\Http\Repositories\User\UserRepository;
use App\Http\Requests\Auth\GoogleRequest;
use App\Http\Response\Facades\ResponseTemplate;
use App\Services\Auth\Facades\AuthService;
use App\Services\User\UserService;
use Google\Service\Drive;
use Google\Service\Oauth2;
use Google_Client;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use OpenApi\Annotations as OA;
use Throwable;

class GoogleController extends BaseController
{
    private array $scope = [
        'openid',
        'email',
    ];

    public function __construct(
        private readonly UserService    $service,
        private readonly UserRepository $repository,
    )
    {
    }

    public function redirectToGoogle(GoogleRequest $request)
    {
        $validated = $request->validated();
        $redirect = $validated['redirect'] ?? '/';
        return Socialite::driver('google')
            ->scopes($this->scope)
            ->stateless()
            ->with(['state' => json_encode(['redirect' => $redirect])])
            ->redirect();
        $url .= '&custom_param=' . $redirect; // Append your custom parameter to the URL
        return redirect($url);
    }

    public function handleGoogleCallbackAndRedirect(Request $request)
    {
        $state = json_decode($request->input('state', '{}'), true);
        $redirect = $state['redirect'] ?? '/';
        $callback = function () use ($redirect) {
            $type = 'login';
            $user = Socialite::driver('google')->stateless()->user();
            $findUser = $this->repository->findUserFromGoogle($user->id);

            /**
             * Google client
             */
            $client = $this->getClient();

            /**
             * Set the access token with google. nb json
             */
            $client->setAccessToken($user->token);

            if (!$findUser) {
                $findUser = $this->repository->createWithInformation([
                    'name' => $this->service->createName(),
                    'email' => $user->getEmail(),
                    'google_id' => $user->getId(),
                    'locale' => $user->user['locale'] ?? 'en',
                ]);

                $type = 'sign-up';
                UserJoinEvent::dispatch($findUser);
            }

            $success['token'] = $findUser->createToken('authToken')->accessToken;
            $url = config('services.google.front_redirect') . "?token={$success['token']}&redirect=$redirect&type=$type";
            return redirect()->away($url);
        };
        $catch = function (Throwable $throwable) {
            if ($throwable instanceof InvalidStateException) {
                $error = 'InvalidStateException';
            } else {
                $error = $throwable->getMessage();
            }
            return redirect()->away(config('services.google.front_redirect') . '?error=' . $error);
        };
        return $this->transaction($callback, $catch);
    }

    /**
     * Gets a google client
     *
     * @return \Google_Client
     * INCOMPLETE
     */
    private function getClient(): Google_Client
    {
        // define an application name
        $applicationName = config('app.name');

        // create the client
        $client = new Google_Client();
        $client->setApplicationName($applicationName);
        $client->setAuthConfig([
            'web' => [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uris' => [config('services.google.redirect')],
            ],
        ]);
        $client->setAccessType('offline');   // necessary for getting the refresh token
        $client->setApprovalPrompt('force'); // necessary for getting the refresh token
        // scopes determine what google endpoints we can access. keep it simple for now.
        $client->setScopes(
            [
                Oauth2::USERINFO_PROFILE,
                Oauth2::USERINFO_EMAIL,
                Oauth2::OPENID,
                Drive::DRIVE_METADATA_READONLY // allows reading of google drive metadata
            ]
        );
        $client->setIncludeGrantedScopes(true);
        return $client;
    } // getClient

    /**
     * @OA\Get(
     *     path="/third/auth/test",
     *     summary="get test access token",
     *     tags={"auth"},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *              type="object",
     *               @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="A simple of response message",
     *              ),
     *               @OA\Property(
     *                 property="data",
     *                 type="object",
     *                  @OA\Property(property="token", type="string", example="123124sdf12`31r2fw"),
     *                  @OA\Property(property="user", type="object", ref="#/components/schemas/UserResources"),
     *                ),
     *             ),
     *         ),
     *      ),
     * )
     * @throws \Throwable
     */
    public function generateTestToken()
    {
        if (!config('app.debug')) abort(404, 'Not Found');
        $user = AuthService::testUser();
        $success['token'] = $user->createToken('authToken')->accessToken;
        $success['user'] = $user;
        return ResponseTemplate::toJson($success, 'User login successfully.');
    }

    public function generatePluginToken(Request $request)
    {
        $user = AuthService::pluginUser();
        $success['token'] = $user->createToken('authToken')->accessToken;
        return ResponseTemplate::toJson($success, 'User login successfully.');
    }
}
