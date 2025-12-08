// Small app bootstrap for dev layout
(function(){
  // Attach CSRF token to fetch requests if present
  if (window.csrfName){
    // nothing to do in stub; real apps can use fetch wrappers here
    console.log('CSRF token available (stub)');
  }
})();
