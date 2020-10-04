@csrf
<div class="form-group row">
    <label class="control-label">{{$setting->key}}<span class="text-danger">*</span></label>
    <select name="value" class='form-control flat'>
        <option value="1" @if($setting && $setting->value=="1") selected @endif>True</option>
        <option value="0" @if($setting && $setting->value=="0") selected @endif>False</option>
    </select>
</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="Edit">
</div>
