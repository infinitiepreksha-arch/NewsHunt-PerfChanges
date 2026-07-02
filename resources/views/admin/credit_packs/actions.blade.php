<div class="btn-group">
    <a href="{{ route('credit-packs.edit', $row->id) }}" class="btn btn-sm btn-info">
        <i class="fas fa-edit"></i>
    </a>
    <form action="{{ route('credit-packs.destroy', $row->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>
