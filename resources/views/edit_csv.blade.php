@extends('include.app')


@section('script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
<script>
  var language = <?php echo json_encode($language); ?>;
  var itemBaseUrl = <?php echo json_encode($itemBaseUrl); ?>;
</script>

<script src="{{ asset('assets/script/csv.js') }}"></script>

@endsection
@section('content')

<div class="card mb-2">
  <div class="card-body p-2">
    <div> <b>{{ $language->title }} </b> - {{ $language->localized_title }} </div>
  </div>
</div>
<ul>
  <li> <b> To edit a Key or Value : </b> Double-click on it, make your changes, and then save. </li>
  <li> <b> To delete : </b> Select the row number, press the Delete key on your keyboard, and then save. </li>
  <li> <b> To add a new row : </b> Click on "Add Row". </li>
  <li> <b> To translate : </b> </li>
  <ol>
    <li> Select the keys using Shift for multiple selection. </li>
    <li> Copy them using Ctrl + C (or Cmd + C on Mac). </li>
    <li> Go to Google Translate, paste the copied text, and translate it. </li>
    <li> Copy the translated text, return to this page, and paste it into the Value section, then save. </li>
  </ol>
</ul>
<div class="card">
  <div class="card-header d-flex align-content-center justify-content-between pb-0">
    <div class="">
      <label for="csvFile" class="btn btn-info">Load CSV</label>
      <input type="file" id="csvFile" class="file-input d-none" accept=".csv" />
      <button class="btn btn-success" id="downloadCSV">Download CSV</button>
    </div>
    <div class="">
      <button class="btn btn-secondary" id="addRow">Add Row</button>
      <button class="btn btn-primary" id="saveFile">
          <span class="spinner-border spinner-border-sm me-1 spinner hide" role="status" aria-hidden="true"></span>
          Save</button>
      <input type="hidden" id="language_id" value="{{ $language->id }}">
      <input type="hidden" id="code" value="{{ $language->code }}" placeholder="Code">
      <input type="hidden" id="title" value="{{ $language->title }}" placeholder="Title">
      <input type="hidden" id="localized_title" value="{{ $language->localized_title }}" placeholder="Localized Title">
    </div>
    <button
      class="btn btn-secondary d-none"
      id="deleteBtn"
      disabled>
      Delete
    </button>
    <button
      class="btn btn-secondary d-none"
      id="copyBtn">
      Copy
    </button>
    <button
      class="btn btn-secondary d-none"
      onclick="pasteFromClipboard()"
      id="pasteBtn">
      Paste
    </button>
  </div>
 <div class="card-body">
    <div class="table-container overflow-x-hidden" id="tableContainer">
      <div id="csvTable">
        <thead id="tableHeader" class="table-light"></thead>
        <tbody id="tableBody"></tbody>
      </div>
      <div
        class="selection-box"
        id="selectionBox"
        style="display: none"></div>
    </div>
  </div>

  <div class="stats">
    <span id="statsText">No data loaded</span>
  </div>
</div>



@endsection
