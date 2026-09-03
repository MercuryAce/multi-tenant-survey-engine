<?php
declare(strict_types=1);

namespace Organisation\Controller;

use AdminKit\Controller\CrudController;

class OrganisationsController extends CrudController
{
    protected string $resourceTable = 'Organisation.Organisations';
    protected string $singular = 'organisation';
    protected string $plural = 'organisations';
    protected array $indexFields = ['id', 'name'];
    protected array $formFields = ['name', 'logo_path'];
}
