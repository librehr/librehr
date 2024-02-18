<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\Models\Documentable
 *
 * @property int $id
 * @property int $document_id
 * @property int $documentable_id
 * @property string $documentable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Documentable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Documentable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Documentable query()
 * @method static \Illuminate\Database\Eloquent\Builder|Documentable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Documentable whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Documentable whereDocumentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Documentable whereDocumentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Documentable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Documentable whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Documentable extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function type()
    {
        return $this->belongsTo(DocumentsType::class, 'documents_type_id', 'id');
    }
}
