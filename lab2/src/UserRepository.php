<?php

namespace App;

use InvalidArgumentException;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;

class UserRepository
{
    public function __construct(
        private readonly Collection $collection
    ) {
    }

    /**
     * @throws UserException
     */
    public function create(User $user): void
    {
        if ($user->getId() !== null) {
            throw new UserException('User is already created');
        }

        if ($this->isExistsWithName($user->getName())) {
            throw new UserException('User with this name is already exists');
        }

        $result = $this->collection->insertOne([
            'name' => $user->getName(),
            'password' => $user->getPassword(),
            'roles' => $user->getRoles()
        ]);

        $user->setId($result->getInsertedId());
    }

    public function findByName(string $name): ?UserDto
    {
        /** @var BSONDocument|null $result */
        $result = $this->collection->findOne(['name' => $name])?->getArrayCopy();

        return $result !== null ? new UserDto($result['name'], $result['password'], $result['roles']->getArrayCopy()) : null;
    }

    public function findById(string $id): ?array
    {
        try {
            $id = new ObjectId($id);
        } catch (InvalidArgumentException) {
            return null;
        }

        /** @var BSONDocument|null $result */
        $result = $this->collection->findOne(['_id' => $id], ['projection' => ['name' => 1, 'roles' => 1, '_id' => 0]]);

        return $result?->getArrayCopy();
    }

    public function isExistsWithName(string $name): bool
    {
        return $this->collection->countDocuments(['name' => $name]) !== 0;
    }

    public function deleteById(string $id): void
    {
        $this->collection->deleteOne(['_id' => new ObjectId($id)]);
    }
}