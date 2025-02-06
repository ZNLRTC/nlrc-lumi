<div class="option-con">
    <input type="checkbox"
    wire:model="options.{{$key}}.checked"
    wire:click="add_answer('{{$question['latest_version']['id']}}','{{$option['id']}}','check')"
    value = "{{ $option['option'] ?? '' }}"
    {{ empty($option['option']) ? 'disabled' : '' }}
    {{ $option['checked'] ? 'checked' : '' }}
    >
    <textarea class="option" rows=1 placeholder="Option"
    wire:model="options.{{$key}}.option"
    wire:change="update_content({{$key}},event.target.value,null)"
    required></textarea>
    <div class="option-actions">
        @if(count($this->filter_array($question['latest_version']['id'])) > 2)
            @include('livewire/quiz/partials/remove-option-button')
        @endif
        @include('livewire/quiz/partials/add-option-button')
    </div>
</div>