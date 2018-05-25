<section id="forma" class="forma">
  <div class="container">
    <div class="row">
      <div class="col-12 text-center feedback">
        <span class="underline_arrow"> Your Booking </span>
        <div id="feedback">
          test
        </div>
        <form class="feedback reservation-form" name="feedback">
          <div class="row">
            <div class="col-12 input-group">
            <select class="form-control form-control-lg">
              <option class="choosen_car" value="" selected>Choose Car</option>
              <option>Large select</option>
            </select>
            </div>
          </div>
          <div class="row">
            <div class="col-6 input-group">
              <span class="input-group-addon" id="sizing-addon1"><img src="<?= get_template_directory_uri(); ?>/assets/images/form_location.svg" alt="Web developer"/> PICK-UP</span>
<input type="text" class="form-control" placeholder="Port, Airport or Hotel address" aria-describedby="sizing-addon1">
            </div>
            <div class="col-4 input-group  input-daterange">
              <span class="input-group-addon" id="sizing-addon1"><img src="<?= get_template_directory_uri(); ?>/assets/images/form_date.svg" alt="Web developer"/> PICK-UP</span>
                <input type="text" class="input-sm form-control" name="start" />
            </div>
            <div class="col-2 input-group bootstrap-timepicker timepicker">
              <span class="input-group-addon" id="sizing-addon1"><img src="<?= get_template_directory_uri(); ?>/assets/images/form_time.svg" alt="Web developer"/></span>
               <input id="timepicker1" type="text" class="form-control input-small">
            </div>
          </div>
          <div class="row">
            <div class="col-6 input-group">
              <span class="input-group-addon" id="sizing-addon1"><img src="<?= get_template_directory_uri(); ?>/assets/images/form_location.svg" alt="Web developer"/> DROP-OFF</span>

<input type="text" class="form-control" placeholder="Username" aria-describedby="sizing-addon1">
            </div>
            <div class="col-4 input-group  input-daterange">
              <span class="input-group-addon" id="sizing-addon1"><img src="<?= get_template_directory_uri(); ?>/assets/images/form_date.svg" alt="Web developer"/> DROP-OFF</span>
                <input type="text" class="input-sm form-control" name="end" />
            </div>
            <div class="col-2 input-group bootstrap-timepicker timepicker">
              <span class="input-group-addon" id="sizing-addon1"><img src="<?= get_template_directory_uri(); ?>/assets/images/form_time.svg" alt="Web developer"/></span>
               <input id="timepicker2" type="text" class="form-control input-small">
            </div>
          </div>
<hr/>
<div class="row">
  <strong>Name</strong>
  <br />
  <input type="text" name="name" class="input-xlarge" value="">
  <br /><br /><strong>Email</strong><br />
  <input type="email" name="email" class="input-xlarge" value="">
  <br /><br /><strong>Message</strong><br />
  <textarea name="message" class="input-xlarge"></textarea>
</div>
</form>

      <button class="btn btn-primary btn-lg" id="submit">Get Quote!</button>

      </div>
    </div>
  </div>

 </section>
