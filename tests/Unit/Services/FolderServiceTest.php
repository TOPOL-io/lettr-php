<?php

declare(strict_types=1);

use Lettr\Collections\FolderCollection;
use Lettr\Dto\Folder\Folder;
use Lettr\Dto\Folder\ListFoldersFilter;
use Lettr\Enums\TemplatePurpose;
use Lettr\Responses\ListFoldersResponse;
use Lettr\Services\FolderService;
use Tests\Support\MockTransporter;

/**
 * @return array<string, mixed>
 */
function folderPayload(int $id, string $name, string $purpose, int $templatesCount = 0): array
{
    return [
        'id' => $id,
        'name' => $name,
        'project_id' => 5,
        'purpose' => $purpose,
        'templates_count' => $templatesCount,
        'created_at' => '2026-01-15T10:00:00+00:00',
        'updated_at' => '2026-01-20T14:30:00+00:00',
    ];
}

test('can create FolderService instance', function (): void {
    $service = new FolderService(new MockTransporter);

    expect($service)->toBeInstanceOf(FolderService::class);
});

test('list method returns ListFoldersResponse', function (): void {
    $transporter = new MockTransporter;
    $transporter->response = [
        'folders' => [
            folderPayload(10, 'Emails', 'transactional', 12),
            folderPayload(11, 'Campaigns', 'campaign', 3),
        ],
        'pagination' => [
            'current_page' => 1,
            'last_page' => 1,
            'per_page' => 25,
            'total' => 2,
        ],
    ];

    $service = new FolderService($transporter);
    $response = $service->list();

    expect($transporter->lastUri)->toBe('folders')
        ->and($transporter->lastQuery)->toBe([])
        ->and($response)->toBeInstanceOf(ListFoldersResponse::class)
        ->and($response->folders)->toBeInstanceOf(FolderCollection::class)
        ->and($response->folders->count())->toBe(2)
        ->and($response->pagination->total)->toBe(2)
        ->and($response->hasMore())->toBeFalse();
});

test('list method sends every filter', function (): void {
    $transporter = new MockTransporter;
    $transporter->response = [
        'folders' => [],
        'pagination' => [
            'current_page' => 2,
            'last_page' => 3,
            'per_page' => 10,
            'total' => 25,
        ],
    ];

    $service = new FolderService($transporter);
    $service->list(new ListFoldersFilter(
        projectId: 5,
        purpose: TemplatePurpose::Campaign,
        perPage: 10,
        page: 2,
    ));

    expect($transporter->lastUri)->toBe('folders')
        ->and($transporter->lastQuery)->toBe([
            'project_id' => 5,
            'purpose' => 'campaign',
            'per_page' => 10,
            'page' => 2,
        ]);
});

test('Folder DTO from array', function (): void {
    $folder = Folder::from(folderPayload(10, 'Campaigns', 'campaign', 12));

    expect($folder->id)->toBe(10)
        ->and($folder->name)->toBe('Campaigns')
        ->and($folder->projectId)->toBe(5)
        ->and($folder->purpose)->toBe(TemplatePurpose::Campaign)
        ->and($folder->templatesCount)->toBe(12)
        ->and($folder->createdAt->toIso8601())->toBe('2026-01-15T10:00:00+00:00')
        ->and($folder->updatedAt->toIso8601())->toBe('2026-01-20T14:30:00+00:00');
});

test('Folder DTO defaults purpose and templates count when the API omits them', function (): void {
    $folder = Folder::from([
        'id' => 10,
        'name' => 'Emails',
        'project_id' => 5,
        'created_at' => '2026-01-15T10:00:00+00:00',
        'updated_at' => '2026-01-20T14:30:00+00:00',
    ]);

    expect($folder->purpose)->toBe(TemplatePurpose::Transactional)
        ->and($folder->templatesCount)->toBe(0);
});

test('ListFoldersFilter fluent API', function (): void {
    $filter = ListFoldersFilter::create()
        ->projectId(5)
        ->purpose(TemplatePurpose::Campaign)
        ->perPage(20)
        ->page(3);

    expect($filter->projectId)->toBe(5)
        ->and($filter->purpose)->toBe(TemplatePurpose::Campaign)
        ->and($filter->perPage)->toBe(20)
        ->and($filter->page)->toBe(3)
        ->and($filter->hasFilters())->toBeTrue()
        ->and($filter->toArray())->toBe([
            'project_id' => 5,
            'purpose' => 'campaign',
            'per_page' => 20,
            'page' => 3,
        ]);
});

test('ListFoldersFilter hasFilters returns false when empty', function (): void {
    $filter = ListFoldersFilter::create();

    expect($filter->hasFilters())->toBeFalse()
        ->and($filter->toArray())->toBe([]);
});

test('FolderCollection finders', function (): void {
    $folders = FolderCollection::from([
        Folder::from(folderPayload(10, 'Emails', 'transactional')),
        Folder::from(folderPayload(11, 'Campaigns', 'campaign')),
    ]);

    expect($folders->count())->toBe(2)
        ->and($folders->isEmpty())->toBeFalse()
        ->and($folders->first()?->name)->toBe('Emails')
        ->and($folders->findById(11)?->name)->toBe('Campaigns')
        ->and($folders->findById(999))->toBeNull()
        ->and($folders->findByName('Campaigns')?->id)->toBe(11)
        ->and($folders->findByName('Nonexistent'))->toBeNull()
        ->and($folders->filterByPurpose(TemplatePurpose::Campaign)->count())->toBe(1);
});

test('FolderCollection empty', function (): void {
    $folders = FolderCollection::empty();

    expect($folders->count())->toBe(0)
        ->and($folders->isEmpty())->toBeTrue()
        ->and($folders->first())->toBeNull();
});

test('FolderCollection can be iterated', function (): void {
    $folders = FolderCollection::from([
        Folder::from(folderPayload(10, 'Emails', 'transactional')),
        Folder::from(folderPayload(11, 'Campaigns', 'campaign')),
    ]);

    $ids = [];
    foreach ($folders as $folder) {
        $ids[] = $folder->id;
    }

    expect($ids)->toBe([10, 11]);
});

test('FolderPagination has next and previous page', function (): void {
    $transporter = new MockTransporter;
    $transporter->response = [
        'folders' => [],
        'pagination' => [
            'current_page' => 2,
            'last_page' => 5,
            'per_page' => 10,
            'total' => 45,
        ],
    ];

    $response = (new FolderService($transporter))->list();

    expect($response->hasMore())->toBeTrue()
        ->and($response->pagination->hasNextPage())->toBeTrue()
        ->and($response->pagination->hasPreviousPage())->toBeTrue()
        ->and($response->pagination->nextPage())->toBe(3)
        ->and($response->pagination->previousPage())->toBe(1);
});
