<?php

namespace App\Models\Common;
use App\Concerns\Blamable;
use App\Concerns\CompanyOwned;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    use Blamable;
    use CompanyOwned;
    protected $table = 'attachments';
    protected $fillable = [
        'filename',
        'filepath',
        'attachable_type',
        'attachable_id',
        'created_by',
        'updated_by',
        'company_id',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function createWithRelations(array $data): self
    {
        dd( $data);
        // Handle file upload if 'file' key exists in $data
        if (isset($data['file'])) {
            $file = $data['file'];
            $filepath = $file->store('attachments', 'public'); // Store file in 'attachments' directory on 'public' disk
            $data['filepath'] = $filepath;
            $data['filename'] = $file->getClientOriginalName(); // Set original filename
            unset($data['file']); // Remove the file from data array to avoid mass assignment issues
        }

        return self::create($data);
    }

}
