<div class="module-small bg-dark">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-6 col-md-6 col-lg-4 col-lg-offset-2">
        <div class="callout-text font-alt">
          <h3 class="callout-title">Εγγραφή στο Newsletter</h3>
          <p>Χωρίς spam.</p>
        </div>
      </div>
      <div class="col-sm-6 col-md-6 col-lg-4">
        <div class="callout-btn-box">
          <form id="subscription-form" role="form" method="post" action="php/subscribe.php">
            <div class="input-group">
              <input class="form-control" type="email" id="semail" name="semail" placeholder="Το Email σας" data-validation-required-message="Please enter your email address." required="required"/><span class="input-group-btn">
                <button class="btn btn-g btn-round" id="subscription-form-submit" type="submit">Αποστολή</button></span>
            </div>
          </form>
          <div class="text-center" id="subscription-response"></div>
        </div>
      </div>
    </div>
  </div>
</div>
