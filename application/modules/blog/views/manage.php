<h1>Blog</h1>

<?php
if (isset($flash)) {
  echo $flash;
}

$create_blog_url = base_url() . "blog/create";
?>
<a href="<?php echo $create_blog_url; ?>">
  <button type="button" class="btn btn-primary" style="margin-top: 30px; margin-bottom: 30px;">Create New Blog Entry</button>
</a>

<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="halflings-icon white file"></i><span class="break"></span>Custom Blog</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
      </div>
    </div>
    <div class="box-content">
      <table class="table table-striped table-bordered bootstrap-datatable datatable">
        <thead>
          <tr>
            <th>Picture</th>
            <th>Date Published</th>
            <th>Author</th>
            <th>Blog URL</th>
            <th>Blog Headline</th>
            <th class="span2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $this->load->module("timedate");

          foreach ($query->result() as $row) :
            $view_blog_url = base_url() . $row->blog_url;
            $edit_blog_url = base_url() . "blog/create/" . $row->id;
            $date_published = $this->timedate->get_nice_date($row->date_published, "mini");

            $picture = $row->picture;
            $thumbnail = str_replace(".", "_thumb.", $picture);
            $thumbnail_path = base_url() . "uploads/blog_pics/" . $thumbnail;
            ?>
            <tr>
              <td><img src="<?php echo $thumbnail_path; ?>" alt="thumbnail"></td>
              <td><?php echo $date_published; ?></td>
              <td><?php echo $row->author; ?></td>
              <td><?php echo $view_blog_url; ?></td>
              <td class="center"><?php echo $row->blog_title; ?></td>
              <td class="center">
                <a class="btn btn-success" href="<?php echo $view_blog_url; ?>">
                  <i class="halflings-icon white zoom-in"></i>
                </a>
                <a class="btn btn-info" href="<?php echo $edit_blog_url; ?>">
                  <i class="halflings-icon white edit"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div><!--/span-->

</div><!--/row-->