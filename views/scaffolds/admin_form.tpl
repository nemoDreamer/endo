<div class="input text">
  <label for="name">Name:</label>
  <input type="text" name="name" value="{$item->name}" pre="{$url.modelName} Name" id="name" class="span-6">
</div>
<div class="input textarea">
  <label for="description">Description:</label>
  <textarea name="description" rows="10" class="span-12" pre="Description">{$item->description}</textarea>
</div>
