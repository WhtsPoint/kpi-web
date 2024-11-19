<?php

namespace App;

use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;

class UserController
{
    public function __construct(
        private readonly JWT $jwt,
        private readonly UserRepository $userRepository
    ) {
    }

    public function create(Request $request, Response $response, array $params): void
    {
        $body = (new ContentValidator($request, $response))->validateJsonAndSerialize();

        if ($body === false) {
            return;
        }

        if (is_string($body['username'] ?? null) === false || is_string($body['password'] ?? null) === false) {
            $response->status(422);
            $response->end(json_encode(['error' => 'Missing necessary parameters']));

            return;
        }

        $user = new User($body['username'], $body['password']);
        $user->addRole('user');

        try {
            $this->userRepository->create($user);
        } catch (UserException $exception) {
            $response->status(400);
            $response->end(json_encode(['error' => $exception->getMessage()]));

            return;
        }

        $response->status(201);
        $response->end(json_encode(['id' => $user->getId()]));
    }

    public function get(Request $request, Response $response, array $params): void
    {
        $user = $this->userRepository->findById($params['id'][0]);

        if (is_array($user) === false) {
            $response->status(404);
            $response->end(json_encode(['error' => 'User not found']));

            return;
        }

        $response->status(200);
        $response->end(json_encode($user));
    }

    public function auth(Request $request, Response $response, array $params): void
    {
        $body = (new ContentValidator($request, $response))->validateJsonAndSerialize();

        if ($body === false) {
            return;
        }

        if (is_string($body['username'] ?? null) === false || is_string($body['password'] ?? null) === false) {
            $response->status(422);
            $response->end(json_encode(['error' => 'Missing necessary parameters']));

            return;
        }

        $user = $this->userRepository->findByName($body['username']);

        if ($user === null) {
            $response->status(404);
            $response->end(json_encode(['error' => 'User not found']));

            return;
        }

        if (User::isValidPassword($body['password'], $user->password) === false) {
            $response->status(400);
            $response->end(json_encode(['error' => 'Wrong password']));

            return;
        }

        $response->status(200);
        $response->end(json_encode(['accessToken' => $this->jwt->createToken([
            'username' => $user->name,
            'roles' => $user->roles,
            'exp' => time() + 3600
        ])]));
    }

    public function delete(Request $request, Response $response, array $params): void
    {
        $tokenPayload = (new JWTValidator($this->jwt))->validateTokenAndSerialize($request, $response);

        if ($tokenPayload === null) {
            return;
        }

        $user = $this->userRepository->findById($params['id'][0]);

        var_dump($user);
        if ($user === null) {
            $response->status(404);
            $response->end(json_encode(['error' => 'User not found']));

            return;
        }

        if ($user['name'] !== $tokenPayload['username'] && in_array('admin', $tokenPayload['roles']) === false) {
            $response->status(403);
            $response->end(json_encode(['error' => 'No approve to delete this user']));

            return;
        }

        $this->userRepository->deleteById($params['id'][0]);

        $response->status(204);
        $response->end();
    }
}