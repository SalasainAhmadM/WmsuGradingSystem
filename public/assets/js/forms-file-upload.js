/**
 * File Upload
 */

"use strict";

document.addEventListener("livewire:navigated", () => {
    // previewTemplate: Updated Dropzone default previewTemplate
    // ! Don't change it unless you really know what you are doing
    const previewTemplate = `<div class="dz-preview dz-file-preview">
<div class="dz-details">
  <div class="dz-thumbnail">
    <img data-dz-thumbnail>
    <span class="dz-nopreview">No preview</span>
    <div class="dz-success-mark"></div>
    <div class="dz-error-mark"></div>
    <div class="dz-error-message"><span data-dz-errormessage></span></div>
    <div class="progress">
      <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
    </div>
  </div>
  <div class="dz-filename" data-dz-name></div>
  <div class="dz-size" data-dz-size></div>
</div>
</div>`;

    // ? Start your code from here

    // Basic Dropzone
    // --------------------------------------------------------------------
    const dropzoneBasic = document.querySelector("#dropzone-basic");
    if (dropzoneBasic) {
        const myDropzone = new Dropzone(dropzoneBasic, {
            url: "/upload", // Will be handled by Livewire
            previewTemplate: previewTemplate,
            parallelUploads: 1,
            maxFilesize: 5,
            addRemoveLinks: true,
            maxFiles: 1,
            init: function() {
              this.on("addedfile", function(file) {
                  // When a file is added, assign it to Livewire
                  @this.image = file;
              });
              this.on("removedfile", function(file) {
                  // If a file is removed, reset the Livewire image property
                  @this.image = null;
              });
          }
        });
    }

    // Multiple Dropzone
    // --------------------------------------------------------------------
    const dropzoneMulti = document.querySelector("#dropzone-multi");
    if (dropzoneMulti) {
        const myDropzoneMulti = new Dropzone(dropzoneMulti, {
            previewTemplate: previewTemplate,
            parallelUploads: 1,
            maxFilesize: 5,
            addRemoveLinks: true,
        });
    }
});
