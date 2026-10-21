<div style="margin-bottom: 20px;">
    <label for="tags" style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">
        Tags <span style="font-weight: 400;" class="text-muted">(optional, separate with commas)</span>
    </label>
    <input type="text" name="tags" id="tags" value="{{ $value ?? '' }}" maxlength="255"
           placeholder="travel, food, sri lanka" class="form-control">
    @error('tags')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>
