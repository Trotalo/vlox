mounted() {
    //wait for the tawk api to load
    const interval = setInterval(function() {
      if (window.$_Tawk && window.$_Tawk.downloaded && window.$_Tawk.showWidget instanceof Function) {
        window.$_Tawk.showWidget()
        clearInterval(interval);
      }
    }, 500);

  },