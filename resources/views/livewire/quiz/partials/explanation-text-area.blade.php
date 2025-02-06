<textarea
class="explanation"
rows=1 placeholder="Add an explanation (optional)"
wire:model="questionnaires.{{$index}}.latest_version.explanation"
wire:change="update_content({{$index}}, event.target.value, 'explanation')"
></textarea>