<?php

namespace App\Http\Controllers\User;

use App\Events\Brevo\UserUpdateEvent;
use App\Events\User\UserViewEvent;
use App\Http\Controllers\BaseController;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UploadAvatarUserRequest;
use App\Http\Resources\User\UserResources;
use App\Services\Auth\Facades\AuthService;
use App\Services\User\Contracts\UserServiceContract;
use OpenApi\Annotations as OA;
use AIGenerate\Models\User\User;

class UserController extends BaseController
{
    public function __construct(protected UserServiceContract $service)
    {
        $this->middleware('auth:api')->except(['show']);
    }

    /**
     * Prompt 를 생성 합니다.
     * @OA\Post(
     *     path="/api/user/avatar",
     *     summary="upload avatar",
     *     tags={"user"},
     *     @OA\RequestBody(
     *       @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(ref="#/components/schemas/UploadAvatarUserRequest")
     *       )
     *     ),
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
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/UserResources"),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function currentUpdateAvatar(UploadAvatarUserRequest $request)
    {
        $validated = $request->validated();
        return $this->transaction(function () use ($validated) {
            return new UserResources($this->service->currentUpdateAvatar($validated['avatar']));
        });
    }

    /**
     * @OA\Get(
     *     path="/api/user/{user}",
     *     summary="user information",
     *     tags={"user"},
     *     @OA\Parameter(
     *         description="user id",
     *         in="path",
     *         name="user",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="1", value="1", summary="1"),
     *     ),
     *      @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(ref="#/components/schemas/Result"),
     *             },
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/UserResources"),
     *         ),
     *      ),
     * )
     * @throws \Throwable
     */
    public function show(User $user)
    {
        return $this->run(function () use ($user) {
            if (AuthService::check()) {
                UserViewEvent::dispatch(AuthService::currentUser(), $user);
            }
            $user->count->prompts = $this->service->getPrompts($user);
            return new UserResources($user);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="user information",
     *     tags={"user"},
     *      @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(ref="#/components/schemas/Result"),
     *             },
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/UserResources"),
     *         ),
     *      ),
     * )
     * @throws \Throwable
     */
    public function currentUser()
    {
        return new UserResources($this->service->currentUser());
    }

    /**
     * @OA\Put(
     *     path="/api/user",
     *     summary="update user",
     *     tags={"user"},
     *     @OA\Parameter(
     *         description="name",
     *         in="query",
     *         name="name",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="name", value="name", summary="name"),
     *     ),
     *     @OA\Parameter(
     *         description="introduction",
     *         in="query",
     *         name="introduction",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="introduction", value="introduction", summary="introduction"),
     *     ),
     *      @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(ref="#/components/schemas/Result"),
     *             },
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/UserResources"),
     *         ),
     *      ),
     * )
     * @throws \Throwable
     */
    public function currentUpdate(UpdateUserRequest $request)
    {
        $validated = $request->validated();
        return $this->transaction(function () use ($validated) {
            $user = $this->service->currentUpdate($validated);
            if ($user && isset($validated['name'])) {
                UserUpdateEvent::dispatch($user);
            }
            return new UserResources($user);
        });
    }

}
