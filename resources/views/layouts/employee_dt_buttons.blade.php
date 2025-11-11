<div class="btn-group">
    <button type="button" class="btn btn-info btn-sm ViewBtn" data-id="{{ $id }}" title="View">
        <i class="fa-solid fa-eye"></i>
    </button>
    <a href="{{ route('employee.edit', $id) }}" class="btn bg-navy btn-sm" title="Edit">
        <i class="fa-solid fa-edit"></i>
    </a>
    <button type="button" class="btn btn-danger btn-sm DeleteBtn" data-id="{{ $id }}" title="Delete">
        <i class="fa-solid fa-trash"></i>
    </button>
</div>
