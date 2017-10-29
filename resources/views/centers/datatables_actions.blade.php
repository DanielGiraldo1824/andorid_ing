{!! Form::open(['route' => ['centers.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('centers.show', $id) }}" class='btn btn-primary btn-xs'>
        <i class="glyphicon glyphicon-eye-open"></i>
    </a>
    <a href="{{ route('centers.edit', $id) }}" class='btn btn-warning btn-xs'>
        <i class="glyphicon glyphicon-edit"></i>
    </a>
    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-default btn-xs',
        'onclick' => "return confirm('¿Está seguro que desea eliminar el registro?')"
    ]) !!}
</div>
{!! Form::close() !!}
