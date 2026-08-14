<?php

declare(strict_types=1);

namespace Lettr\Services\Audience;

use Lettr\Contracts\TransporterContract;
use Lettr\Dto\Audience\AudienceContact;
use Lettr\Dto\Audience\BulkAttachContactsToListsData;
use Lettr\Dto\Audience\BulkAttachContactsToListsResult;
use Lettr\Dto\Audience\BulkAudienceContactTopicsData;
use Lettr\Dto\Audience\BulkCreateAudienceContactsData;
use Lettr\Dto\Audience\BulkDetachContactsFromListsData;
use Lettr\Dto\Audience\BulkDetachContactsFromListsResult;
use Lettr\Dto\Audience\BulkStoreAudienceContactsResult;
use Lettr\Dto\Audience\BulkSubscribeContactsToTopicsResult;
use Lettr\Dto\Audience\BulkUnsubscribeContactsFromTopicsResult;
use Lettr\Dto\Audience\CreateAudienceContactData;
use Lettr\Dto\Audience\ListAudienceContactsFilter;
use Lettr\Dto\Audience\UpdateAudienceContactData;
use Lettr\Enums\ErrorCode;
use Lettr\Exceptions\ConflictException;
use Lettr\Exceptions\ContactAlreadyExistsException;
use Lettr\Responses\ListAudienceContactsResponse;

/**
 * Service for managing audience contacts via the Lettr API.
 */
final class AudienceContactService
{
    private const ENDPOINT = 'audience/contacts';

    public function __construct(
        private readonly TransporterContract $transporter,
    ) {}

    public function list(?ListAudienceContactsFilter $filter = null): ListAudienceContactsResponse
    {
        $query = $filter?->toArray() ?? [];

        /**
         * @var array{
         *     contacts: array<int, array{
         *         id: string,
         *         email: string,
         *         status: string,
         *         properties: array<string, string>,
         *         created_at: string,
         *         lists: array<int, array{id: string, name: string}>,
         *         topics: array<int, array{id: string, name: string}>,
         *     }>,
         *     pagination: array{current_page: int, last_page: int, per_page: int, total: int},
         * } $response
         */
        $response = $this->transporter->getWithQuery(self::ENDPOINT, $query);

        return ListAudienceContactsResponse::from($response);
    }

    public function get(string $contactId): AudienceContact
    {
        /**
         * @var array{
         *     id: string,
         *     email: string,
         *     status: string,
         *     properties: array<string, string>,
         *     created_at: string,
         *     lists: array<int, array{id: string, name: string}>,
         *     topics: array<int, array{id: string, name: string}>,
         * } $response
         */
        $response = $this->transporter->get(self::ENDPOINT.'/'.$contactId);

        return AudienceContact::from($response);
    }

    /**
     * Create a single contact.
     *
     * @throws ContactAlreadyExistsException when the email is already in the
     *                                       team's audience. It is a subclass of
     *                                       ConflictException, so existing
     *                                       handlers keep catching it.
     */
    public function create(CreateAudienceContactData $data): AudienceContact
    {
        try {
            /**
             * @var array{
             *     id: string,
             *     email: string,
             *     status: string,
             *     properties: array<string, string>,
             *     created_at: string,
             *     lists: array<int, array{id: string, name: string}>,
             *     topics: array<int, array{id: string, name: string}>,
             * } $response
             */
            $response = $this->transporter->post(self::ENDPOINT, $data->toArray());
        } catch (ConflictException $e) {
            // The only documented 409 on this endpoint is a duplicate email.
            // Any other conflict code the API grows later stays generic.
            if ($e->errorCode === null || $e->errorCode === ErrorCode::ResourceAlreadyExists->value) {
                throw ContactAlreadyExistsException::fromConflict($e, $data->email);
            }

            throw $e;
        }

        return AudienceContact::from($response);
    }

    public function update(string $contactId, UpdateAudienceContactData $data): AudienceContact
    {
        /**
         * @var array{
         *     id: string,
         *     email: string,
         *     status: string,
         *     properties: array<string, string>,
         *     created_at: string,
         *     lists: array<int, array{id: string, name: string}>,
         *     topics: array<int, array{id: string, name: string}>,
         * } $response
         */
        $response = $this->transporter->patch(self::ENDPOINT.'/'.$contactId, $data->toArray());

        return AudienceContact::from($response);
    }

    public function delete(string $contactId): void
    {
        $this->transporter->delete(self::ENDPOINT.'/'.$contactId);
    }

    /**
     * Create up to 1000 contacts in one request.
     *
     * Rows that fail validation are skipped, not fatal: the call still returns
     * HTTP 201 and reports them in the result's `errors`. Check
     * {@see BulkStoreAudienceContactsResult::hasErrors()} — a successful return
     * does not mean every row landed.
     */
    public function bulkCreate(BulkCreateAudienceContactsData $data): BulkStoreAudienceContactsResult
    {
        /**
         * @var array{
         *     created: int,
         *     already_existed: int,
         *     updated?: int,
         *     error_count?: int,
         *     errors?: array<int, array{index: int, email: string|null, error_code: string, error: string}>,
         *     contacts?: array<int, array{id: string, email: string, created: bool}>,
         * } $response
         */
        $response = $this->transporter->post(self::ENDPOINT.'/bulk', $data->toArray());

        return BulkStoreAudienceContactsResult::from($response);
    }

    public function bulkAttachLists(BulkAttachContactsToListsData $data): BulkAttachContactsToListsResult
    {
        /**
         * @var array{attached: int, already_attached: int, total_pairs: int} $response
         */
        $response = $this->transporter->post(self::ENDPOINT.'/lists/bulk', $data->toArray());

        return BulkAttachContactsToListsResult::from($response);
    }

    public function bulkDetachLists(BulkDetachContactsFromListsData $data): BulkDetachContactsFromListsResult
    {
        /**
         * @var array{detached: int, not_present: int, total_pairs: int} $response
         */
        $response = $this->transporter->deleteWithBody(self::ENDPOINT.'/lists/bulk', $data->toArray());

        return BulkDetachContactsFromListsResult::from($response);
    }

    /**
     * Subscribe every (contact × topic) combination
     * (up to 1000 contacts × 50 topics).
     */
    public function bulkSubscribeTopics(BulkAudienceContactTopicsData $data): BulkSubscribeContactsToTopicsResult
    {
        /**
         * @var array{subscribed: int, already_subscribed: int, total_pairs: int} $response
         */
        $response = $this->transporter->post(self::ENDPOINT.'/topics/bulk', $data->toArray());

        return BulkSubscribeContactsToTopicsResult::from($response);
    }

    /**
     * Unsubscribe every (contact × topic) combination. Pairs that do not exist
     * are ignored.
     */
    public function bulkUnsubscribeTopics(BulkAudienceContactTopicsData $data): BulkUnsubscribeContactsFromTopicsResult
    {
        /**
         * @var array{unsubscribed: int, total_pairs: int} $response
         */
        $response = $this->transporter->deleteWithBody(self::ENDPOINT.'/topics/bulk', $data->toArray());

        return BulkUnsubscribeContactsFromTopicsResult::from($response);
    }

    /**
     * Attach a contact to a list.
     *
     * @return bool `true` if the contact was newly attached (HTTP 201),
     *              `false` if the contact was already in the list (HTTP 200).
     */
    public function attachList(string $contactId, string $listId): bool
    {
        $this->transporter->post(self::ENDPOINT.'/'.$contactId.'/lists/'.$listId, []);

        return $this->transporter->lastStatusCode() === 201;
    }

    public function detachList(string $contactId, string $listId): void
    {
        $this->transporter->delete(self::ENDPOINT.'/'.$contactId.'/lists/'.$listId);
    }

    /**
     * Subscribe a contact to a topic.
     *
     * @return bool `true` if the subscription is new (HTTP 201),
     *              `false` if the contact was already subscribed (HTTP 200).
     */
    public function subscribeTopic(string $contactId, string $topicId): bool
    {
        $this->transporter->post(self::ENDPOINT.'/'.$contactId.'/topics/'.$topicId, []);

        return $this->transporter->lastStatusCode() === 201;
    }

    public function unsubscribeTopic(string $contactId, string $topicId): void
    {
        $this->transporter->delete(self::ENDPOINT.'/'.$contactId.'/topics/'.$topicId);
    }
}
