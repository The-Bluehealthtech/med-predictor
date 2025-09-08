<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Searchable
{
    /**
     * Applique les filtres de recherche sur une requête
     */
    public function applySearchFilters(Builder $query, Request $request, array $searchFields = [])
    {
        foreach ($searchFields as $field) {
            $fieldName = $field['name'];
            $fieldType = $field['type'] ?? 'text';
            $searchValue = $request->get($fieldName);

            if (empty($searchValue)) {
                continue;
            }

            switch ($fieldType) {
                case 'text':
                case 'email':
                    $this->applyTextSearch($query, $fieldName, $searchValue, $field);
                    break;
                
                case 'select':
                    $this->applySelectSearch($query, $fieldName, $searchValue, $field);
                    break;
                
                case 'date':
                    $this->applyDateSearch($query, $fieldName, $searchValue, $field);
                    break;
                
                case 'date_range':
                    $this->applyDateRangeSearch($query, $fieldName, $request, $field);
                    break;
                
                case 'number':
                    $this->applyNumberSearch($query, $fieldName, $searchValue, $field);
                    break;
            }
        }

        return $query;
    }

    /**
     * Recherche textuelle
     */
    protected function applyTextSearch(Builder $query, string $fieldName, string $searchValue, array $field)
    {
        $searchColumns = $field['columns'] ?? [$fieldName];
        $searchMode = $field['mode'] ?? 'contains'; // contains, starts_with, exact

        $query->where(function ($q) use ($searchColumns, $searchValue, $searchMode) {
            foreach ($searchColumns as $column) {
                // Vérifier si c'est une relation (contient un point)
                if (strpos($column, '.') !== false) {
                    $this->applyRelationSearch($q, $column, $searchValue, $searchMode);
                } else {
                    // Recherche directe sur la colonne
                    switch ($searchMode) {
                        case 'starts_with':
                            $q->orWhere($column, 'LIKE', $searchValue . '%');
                            break;
                        case 'exact':
                            $q->orWhere($column, '=', $searchValue);
                            break;
                        case 'contains':
                        default:
                            $q->orWhere($column, 'LIKE', '%' . $searchValue . '%');
                            break;
                    }
                }
            }
        });
    }

    /**
     * Recherche dans les relations
     */
    protected function applyRelationSearch(Builder $query, string $relationColumn, string $searchValue, string $searchMode)
    {
        $parts = explode('.', $relationColumn);
        $relation = $parts[0];
        $column = $parts[1];

        $query->orWhereHas($relation, function ($q) use ($column, $searchValue, $searchMode) {
            switch ($searchMode) {
                case 'starts_with':
                    $q->where($column, 'LIKE', $searchValue . '%');
                    break;
                case 'exact':
                    $q->where($column, '=', $searchValue);
                    break;
                case 'contains':
                default:
                    $q->where($column, 'LIKE', '%' . $searchValue . '%');
                    break;
            }
        });
    }

    /**
     * Recherche par sélection
     */
    protected function applySelectSearch(Builder $query, string $fieldName, string $searchValue, array $field)
    {
        $column = $field['column'] ?? $fieldName;
        
        // Vérifier si c'est une relation (contient un point)
        if (strpos($column, '.') !== false) {
            $parts = explode('.', $column);
            $relation = $parts[0];
            $relationColumn = $parts[1];
            
            $query->whereHas($relation, function ($q) use ($relationColumn, $searchValue) {
                $q->where($relationColumn, '=', $searchValue);
            });
        } else {
            $query->where($column, '=', $searchValue);
        }
    }

    /**
     * Recherche par date
     */
    protected function applyDateSearch(Builder $query, string $fieldName, string $searchValue, array $field)
    {
        $column = $field['column'] ?? $fieldName;
        $query->whereDate($column, '=', $searchValue);
    }

    /**
     * Recherche par plage de dates
     */
    protected function applyDateRangeSearch(Builder $query, string $fieldName, Request $request, array $field)
    {
        $column = $field['column'] ?? $fieldName;
        $fromValue = $request->get($fieldName . '_from');
        $toValue = $request->get($fieldName . '_to');

        if ($fromValue) {
            $query->whereDate($column, '>=', $fromValue);
        }

        if ($toValue) {
            $query->whereDate($column, '<=', $toValue);
        }
    }

    /**
     * Recherche numérique
     */
    protected function applyNumberSearch(Builder $query, string $fieldName, string $searchValue, array $field)
    {
        $column = $field['column'] ?? $fieldName;
        $operator = $field['operator'] ?? '=';

        if (is_numeric($searchValue)) {
            $query->where($column, $operator, $searchValue);
        }
    }

    /**
     * Applique la pagination
     */
    public function applyPagination(Builder $query, Request $request, int $perPage = 20)
    {
        return $query->paginate($perPage)->appends($request->query());
    }

    /**
     * Prépare les données pour le composant de recherche
     */
    public function prepareSearchData($paginatedResults, array $searchFields, Request $request)
    {
        return [
            'searchFields' => $searchFields,
            'currentPage' => $paginatedResults->currentPage(),
            'totalPages' => $paginatedResults->lastPage(),
            'perPage' => $paginatedResults->perPage(),
            'totalItems' => $paginatedResults->total(),
            'searchParams' => $request->query()
        ];
    }
}
