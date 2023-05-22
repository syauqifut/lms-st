<template>
  <div id="adobe-dc-view" :style="'height: ' + height + 'px; width: ' + width + 'px;'"></div>
</template>

<script>
export default {
  props: {
    height: {
      type: Number,
      default: 500
    },
    width: {
      type: Number,
      default: 360
    },
    pdfurl: String,
    pdfTitle: String,
  },
  
  mounted(){
     let pdfScript = document.createElement('script')
      pdfScript.setAttribute('src', 'https://documentcloud.adobe.com/view-sdk/main.js')
      document.head.appendChild(pdfScript);

    let pdf = this.pdfurl
    let title = this.pdfTitle
    document.addEventListener("adobe_dc_view_sdk.ready", function() { 
      var adobeDCView = new AdobeDC.View({clientId: "22e1459cc0014253afabf73826f77aff", divId: "adobe-dc-view"});
      adobeDCView.previewFile({
        content:{location: {url: pdf}},
        metaData:{fileName: title}
      }, {embedMode: "SIZED_CONTAINER"});
    });
  },
}
</script>
