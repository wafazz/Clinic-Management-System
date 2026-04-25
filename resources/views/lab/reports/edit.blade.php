<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Enter Results: {{ $labReport->report_number }}</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('lab-reports.update', $labReport) }}" >
                @csrf @method('PUT')

                <div class="bg-light rounded p-3 text-sm mb-4">
                    <strong>Patient:</strong> {{ $labReport->patient->name }} |
                    <strong>Doctor:</strong> {{ $labReport->doctor->user->name ?? '-' }} |
                    <strong>Report:</strong> {{ $labReport->report_number }}
                </div>

                <table class="table table-hover">
                    <thead ><tr>
                        <th >Test</th>
                        <th >Normal Range</th>
                        <th >Result</th>
                        <th >Abnormal</th>
                        <th >Notes</th>
                    </tr></thead>
                    <tbody>
                        @foreach($labReport->items as $i => $item)
                            <input type="hidden" name="results[{{ $i }}][lab_report_item_id]" value="{{ $item->id }}" />
                            <tr class="border-t">
                                <td >{{ $item->test->name }}
                                    @if($item->test->unit) <span class="text-muted">({{ $item->test->unit }})</span> @endif
                                </td>
                                <td class="text-muted">{{ $item->test->normal_range ?? '-' }}</td>
                                <td >
                                    <input type="text" name="results[{{ $i }}][result]" value="{{ old("results.{$i}.result", $item->result) }}" class="form-control" />
                                </td>
                                <td >
                                    <input type="checkbox" name="results[{{ $i }}][is_abnormal]" value="1" {{ old("results.{$i}.is_abnormal", $item->is_abnormal) ? 'checked' : '' }} class="form-check-input" />
                                </td>
                                <td >
                                    <input type="text" name="results[{{ $i }}][notes]" value="{{ old("results.{$i}.notes", $item->notes) }}" class="form-control" placeholder="Optional" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div>
                    <label class="form-label">Report Notes</label>
                    <textarea name="report_notes" rows="2" class="form-control">{{ old('report_notes', $labReport->notes) }}</textarea>
                </div>

                <div>
                    <label class="form-label">Status *</label>
                    <select name="status" required class="form-control">
                        @foreach(['pending', 'in_progress', 'completed'] as $s)
                            <option value="{{ $s }}" {{ old('status', $labReport->status) === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Save Results</button>
                    <a href="{{ route('lab-reports.show', $labReport) }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
