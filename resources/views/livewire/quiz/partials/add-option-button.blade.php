<button type="button" title="Add Option"
wire:click.prevent="add_option('{{$question['latest_version']['id']}}',{{$key}},null,null)">
    @include('livewire/quiz/svg/add')
</button>