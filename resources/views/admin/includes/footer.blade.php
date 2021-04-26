<footer class="footer">
    <div class="container-fluid">
        <div class="row text-muted">
            <div class="col-6 text-left">
                <p class="mb-0">
                    <a href="https://synctechsol.com" class="text-muted"><strong>Developed By Syntechsol</strong></a> &copy; 2021
                </p>
            </div>
            <div class="col-6 text-right">
                
            </div>
        </div>
    </div>
</footer>

<!-- Modal -->
<div class="modal fade" id="addsms" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{ route('admin.update.sms') }}"> @csrf
        <div class="modal-body">
            <div class="form-group">
                <label>Add SMS<span class="text-darl" >*</span></label>
                <input type="text" value="" class="form-control" required name="sms">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>