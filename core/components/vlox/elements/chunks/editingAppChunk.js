mounted()
{
  setTimeout(() => {
//Tawk section
    const interval = setInterval(function () {
      if (window.$_Tawk && window.$_Tawk.downloaded && window.$_Tawk.showWidget instanceof Function) {
        window.$_Tawk.showWidget()
        clearInterval(interval);
      }
    }, 500);
    //First we pick the document
    let doc = document.getElementById("app").firstChild.firstChild;
    if (doc.offsetHeight === 0 || doc.offsetWidth === 0) {
      doc = document.getElementById("app").firstChild.firstChild.firstChild;
    } else {
      //check if we need to set the tmps style
      const innerChild = document.getElementById("app").firstChild.firstChild.firstChild;
      if (innerChild.offsetWidth !== doc.offsetWidth) {
        doc.style['max-width'] = 'fit-content';
      }
    }
    html2canvas(doc).then(canvas => {
      canvas.toBlob((blob) => {
        const formData = new FormData();
        formData.append('img', blob, 'snapshot.png');

        let axiosConfig = {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        };
        axios.post(window.location.protocol + "//" + window.location.host + this.$restRoute +
          '/rest/index.php?_rest=Blocks/' + "[[+resId]]",
          formData,
          axiosConfig)
          .then(response => {
            console.log(response);
            doc.style['max-width'] = null;
          })
          .catch(error => {
            console.log(error);
          });
      });
    });
  }, 1000);
}
,