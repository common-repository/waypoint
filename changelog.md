### 2018-10-29

- Check for the presence of babel-polyfill then conditionally load it if not present. 
  A conflict was found with another plugin that manually enqueued babel-polyfill, killing the script execution.
- Allow $_FILES to trigger admin settings page hook.
- Remove Region taxonomy, replace with Category.
- Remove location-list widget, short code.