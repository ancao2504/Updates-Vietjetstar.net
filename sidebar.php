<?php
if (is_single()) {
    global $wp_query;
    $postid = $wp_query->post->ID;
    $dep_code = get_post_meta($postid, 'fl_dep_code', true);
    $arv_code = get_post_meta($postid, 'fl_arv_code', true);
    wp_reset_query();
}
?>
    <div id="wgsform" class="mobile-info-pax">
        <?php if (is_page("tim-chuyen-bay")): ?>
            <div class="wgbox wgfilterflight mb30" id="filterflight">
                <h2>Chọn lọc</h2>

                <form id="frmfilterflight">
                    <ul>
                        <li>
                            <label for="filterall" class="checkbox checkbox-custom-label">
                                <input type="checkbox" name="ckfilter" class="flightfilter" value="all" id="filterall" checked="checked"/><span>Tất cả</span>
                            </label>
                        </li>
                        <li class="vna al"><label for="filtervna" class="checkbox checkbox-custom-label"><input
                                    type="checkbox" name="ckfilter" class="flightfilter checkbox-custom" value="vna"
                                    id="filtervna" checked="checked"/><span> VietNam Airlines</span></label></li>
                        <li class="vj al"><label for="filtervj" class="checkbox checkbox-custom-label"><input
                                    type="checkbox" name="ckfilter" class="flightfilter checkbox-custom" value="vj"
                                    id="filtervj" checked="checked"/> <span> Vietjet Air</span></label></li>
                        <li class="js al"><label for="filterjs" class="checkbox checkbox-custom-label"><input
                                    type="checkbox" name="ckfilter" class="flightfilter checkbox-custom" value="js"
                                    id="filterjs" checked="checked"/><span> Jetstar</span></label></li>
                        <li class="qh al"><label for="filterqh" class="checkbox checkbox-custom-label"><input
                                    type="checkbox" name="ckfilter" class="flightfilter checkbox-custom" value="qh"
                                    id="filterqh" checked="checked"/><span> Bamboo Airways</span></label></li>
                    </ul>
                </form>
            </div><!--#filterflight-->
        <?php endif; ?>

        <form class="search-flight-form" action="<?php echo _page("flightresult") ?>" method="post" id="frmwgsearch"
              class="booking-item-dates-change mb30">
            <h2>Tìm chuyến bay</h2>
            <div class="form-group row">
                <div class="col-md-12">
                    <label class="radio radio-inline checkbox-custom-label checked">
                        <input type="radio" class="wgdirection checkbox-custom" name="direction" id="wgoneway" value="1"
                               checked="checked"/>
                        <span>Một chiều </span>
                    </label>
                    <label class="radio radio-inline checkbox-custom-label ">
                        <input type="radio" class="wgdirection checkbox-custom" name="direction" id="wgroundtrip"
                               value="0"/>
                        <span>Khứ hồi  </span>
                    </label>

                </div>
            </div>
            <div class="form-group form-group-icon-left"><i
                    class="fa fa-map-marker input-icon input-icon-hightlight"></i>
                <?php
                $crsource = (isset($dep_code) && !empty($dep_code)) ? $dep_code : (isset($_SESSION["search"]["source"]) && !empty($_SESSION["search"]["source"]) ? $_SESSION["search"]["source"] : 'SGN');
                $crdestination = (isset($arv_code) && !empty($arv_code)) ? $arv_code : (isset($_SESSION["search"]["destination"]) && !empty($_SESSION["search"]["destination"]) ? $_SESSION["search"]["destination"] : 'HAN');
                $crdepdate = isset($_SESSION["search"]["depart"]) ? $_SESSION["search"]["depart"] : date('d/m/Y', strtotime('+3 days'));
                $crretdate = ($_SESSION["search"]["return"]) ? $_SESSION["search"]["return"] : "";
                $cradult = ($_SESSION["search"]["adult"]) ? $_SESSION["search"]["adult"] : 1;
                $crchild = ($_SESSION["search"]["children"]) ? $_SESSION["search"]["children"] : 0;
                $crinfant = ($_SESSION["search"]["infant"]) ? $_SESSION["search"]["infant"] : 0;
                ?>
                <label>Nơi đi</label>

                <select name="dep" id="wgdep" class="form-control">
                    <optgroup label="Miền Bắc">
                        <option value="HAN" <?php echo ($crsource == "HAN") ? "selected='selected'" : ""; ?> >Hà Nội
                        </option>
                        <option value="HPH" <?php echo ($crsource == "HPH") ? "selected='selected'" : ""; ?>>Hải Phòng
                        </option>
                        <option value="VDO" <?php echo ($crsource == "VDO") ? "selected='selected'" : ""; ?>>Vân Đồn
                        </option>
                        <option value="DIN" <?php echo ($crsource == "DIN") ? "selected='selected'" : ""; ?>>Điện Biên
                        </option>
                    </optgroup>
                    <optgroup label="Miền Trung">
                        <option value="THD" <?php echo ($crsource == "THD") ? "selected='selected'" : ""; ?>>Thanh Hóa
                        </option>
                        <option value="VII" <?php echo ($crsource == "VII") ? "selected='selected'" : ""; ?>>Vinh
                        </option>
                        <option value="HUI" <?php echo ($crsource == "HUI") ? "selected='selected'" : ""; ?>>Huế
                        </option>
                        <option value="VDH" <?php echo ($crsource == "VDH") ? "selected='selected'" : ""; ?>>Đồng Hới
                        </option>
                        <option value="DAD" <?php echo ($crsource == "DAD") ? "selected='selected'" : ""; ?>>Đà Nẵng
                        </option>
                        <option value="PXU" <?php echo ($crsource == "PXU") ? "selected='selected'" : ""; ?>>Pleiku
                        </option>
                        <option value="TBB" <?php echo ($crsource == "TBB") ? "selected='selected'" : ""; ?>>Tuy Hòa
                        </option>
                    </optgroup>
                    <optgroup label="Miền Nam">
                        <option value="SGN" <?php echo ($crsource == "SGN") ? "selected='selected'" : ""; ?>>Hồ Chí
                            Minh
                        </option>
                        <option value="NHA" <?php echo ($crsource == "NHA" || $crsource == "CXR") ? "selected='selected'" : ""; ?>>Nha Trang
                        </option>
                        <option value="DLI" <?php echo ($crsource == "DLI") ? "selected='selected'" : ""; ?>>Đà Lạt
                        </option>
                        <option value="PQC" <?php echo ($crsource == "PQC") ? "selected='selected'" : ""; ?>>Phú Quốc
                        </option>
                        <option value="VCL" <?php echo ($crsource == "VCL") ? "selected='selected'" : ""; ?>>Chu Lai
                        </option>
                        <option value="UIH" <?php echo ($crsource == "UIH") ? "selected='selected'" : ""; ?>>Quy Nhơn
                        </option>
                        <option value="VCA" <?php echo ($crsource == "VCA") ? "selected='selected'" : ""; ?>>Cần Thơ
                        </option>
                        <option value="VCS" <?php echo ($crsource == "VCS") ? "selected='selected'" : ""; ?>>Côn Đảo
                        </option>
                        <option value="BMV" <?php echo ($crsource == "BMV") ? "selected='selected'" : ""; ?>>Ban Mê
                            Thuột
                        </option>
                        <option value="VKG" <?php echo ($crsource == "VKG") ? "selected='selected'" : ""; ?>>Rạch Giá
                        </option>
                        <option value="CAH" <?php echo ($crsource == "CAH") ? "selected='selected'" : ""; ?>>Cà Mau
                        </option>
                    </optgroup>
                </select>

            </div>

            <div class="form-group form-group-icon-left"><i
                    class="fa fa-map-marker input-icon input-icon-hightlight"></i>
                <label>Nơi đến</label>

                <select name="des" id="wgdes" class="form-control">
                    <optgroup label="Miền Bắc">
                        <option value="HAN" <?php echo ($crdestination == "HAN") ? "selected='selected'" : ""; ?> >Hà
                            Nội
                        </option>
                        <option value="HPH" <?php echo ($crdestination == "HPH") ? "selected='selected'" : ""; ?>>Hải
                            Phòng
                        </option>
                        <option value="VDO" <?php echo ($crdestination == "VDO") ? "selected='selected'" : ""; ?>>Vân Đồn
                        </option>
                        <option value="DIN" <?php echo ($crdestination == "DIN") ? "selected='selected'" : ""; ?>>Điện
                            Biên
                        </option>
                    </optgroup>
                    <optgroup label="Miền Trung">
                        <option value="THD" <?php echo ($crdestination == "THD") ? "selected='selected'" : ""; ?>>Thanh
                            Hóa
                        </option>
                        <option value="VII" <?php echo ($crdestination == "VII") ? "selected='selected'" : ""; ?>>Vinh
                        </option>
                        <option value="HUI" <?php echo ($crdestination == "HUI") ? "selected='selected'" : ""; ?>>Huế
                        </option>
                        <option value="VDH" <?php echo ($crdestination == "VDH") ? "selected='selected'" : ""; ?>>Đồng
                            Hới
                        </option>
                        <option value="DAD" <?php echo ($crdestination == "DAD") ? "selected='selected'" : ""; ?>>Đà
                            Nẵng
                        </option>
                        <option value="PXU" <?php echo ($crdestination == "PXU") ? "selected='selected'" : ""; ?>>
                            Pleiku
                        </option>
                        <option value="TBB" <?php echo ($crdestination == "TBB") ? "selected='selected'" : ""; ?>>Tuy
                            Hòa
                        </option>
                    </optgroup>
                    <optgroup label="Miền Nam">
                        <option value="SGN" <?php echo ($crdestination == "SGN") ? "selected='selected'" : ""; ?>>Hồ Chí
                            Minh
                        </option>
                        <option value="NHA" <?php echo ($crdestination == "NHA" || $crdestination == "CXR") ? "selected='selected'" : ""; ?>>Nha
                            Trang
                        </option>
                        <option value="DLI" <?php echo ($crdestination == "DLI") ? "selected='selected'" : ""; ?>>Đà
                            Lạt
                        </option>
                        <option value="PQC" <?php echo ($crdestination == "PQC") ? "selected='selected'" : ""; ?>>Phú
                            Quốc
                        </option>
                        <option value="VCL" <?php echo ($crdestination == "VCL") ? "selected='selected'" : ""; ?>>Chu
                            Lai
                        </option>
                        <option value="UIH" <?php echo ($crdestination == "UIH") ? "selected='selected'" : ""; ?>>Quy
                            Nhơn
                        </option>
                        <option value="VCA" <?php echo ($crdestination == "VCA") ? "selected='selected'" : ""; ?>>Cần
                            Thơ
                        </option>
                        <option value="VCS" <?php echo ($crdestination == "VCS") ? "selected='selected'" : ""; ?>>Côn
                            Đảo
                        </option>
                        <option value="BMV" <?php echo ($crdestination == "BMV") ? "selected='selected'" : ""; ?>>Ban Mê
                            Thuột
                        </option>
                        <option value="VKG" <?php echo ($crdestination == "VKG") ? "selected='selected'" : ""; ?>>Rạch
                            Giá
                        </option>
                        <option value="CAH" <?php echo ($crdestination == "CAH") ? "selected='selected'" : ""; ?>>Cà
                            Mau
                        </option>
                    </optgroup>
                </select>

            </div>

            <div class="form-group form-group-icon-left"><i class="fa fa-calendar input-icon input-icon-hightlight"></i>
                <label>Ngày đi</label>

                <input type="text" value="<?php echo $crdepdate ?>" class="dates form-control" name="depdate"
                       id="wgdepdate" placeholder="dd/mm/yyyy" autocomplete="off" readonly="readonly">

            </div>

            <div class="form-group form-group-icon-left"><i class="fa fa-calendar input-icon input-icon-hightlight"></i>
                <label>Ngày về</label>

                <input type="text" value="<?php echo $crretdate ?>" class="dates form-control" name="retdate"
                       id="wgretdate" placeholder="dd/mm/yyyy" autocomplete="off" readonly="readonly">

            </div>

            <div class="form-group form-group-select-plus ">

                <div class="col-md-4 col-md-4 col-sm-4 col-xs-4 wgquantity">
                    <label for="wgadult"><label>Người lớn</label></label>
                    <select id="wgadult" name="adult" class="form-control">
                        <?php for ($i = 1; $i <= 99; $i++): ?>
                            <option
                                value="<?php echo $i ?>" <?php echo ($cradult == $i) ? "selected='selected'" : ""; ?> ><?php echo $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>


                <div class="col-md-4 col-md-4 col-sm-4 col-xs-4 wgquantity">
                    <label for="wgchild"><label>Trẻ em</label></label>
                    <select id="wgchild" name="child" class="form-control">
                        <?php for ($i = 0; $i <= 99; $i++): ?>
                            <option
                                value="<?php echo $i ?>" <?php echo ($crchild == $i) ? "selected='selected'" : ""; ?> ><?php echo $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div><!--fqtity-->

                <div class="col-md-4 col-md-4 col-sm-4 col-xs-4 wgquantity">
                    <label for="wginfant"><label>Em bé</label></label>
                    <select id="wginfant" name="infant" class="form-control">
                        <?php for ($i = 0; $i <= 6; $i++): ?>
                            <option
                                value="<?php echo $i ?>" <?php echo ($crinfant == $i) ? "selected='selected'" : ""; ?> ><?php echo $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>


            </div>
            <div class="gap small-gap mobile-small-gap"></div>


            <button class="button redcus" type="submit" name="wgbtnsearch" id="wgbtnsearch"><i
                    class="fa fa-plane box-icon-border box-icon-left round box-icon-white box-icon-small"></i> <span>Tìm
                chuyến bay</span>
            </button>


    </div>
    </form><!--#frmwgsearch-->

<?php if (is_single() || is_category()) { ?>
    <div class="sidebar-widget">
        <h4>Bài viết xem nhiều</h4>
        <ul class="thumb-list">
            <?php
            global $post;
            $args = array('numberposts' => 5, 'order' => 'DESC', 'orderby' => 'date', 'post_status' => 'publish');
            $myposts = get_posts($args);
            foreach ($myposts as $post) : setup_postdata($post);
                ?>
                <li>
                    <a href="<?php the_permalink(); ?>"><img
                            src="<?php echo(ck_get_featured_image($post->ID) != '' ? ck_get_featured_image($post->ID) : ck_get_content_first_image()); ?>"/></a>
                    <div class="thumb-list-item-caption">
                        <p class="thumb-list-item-meta"><?php the_date("d/m/Y h:m") ?></p>
                        <h5 class="thumb-list-item-title"><a href="<?php the_permalink() ?>"
                                                             title="permalink to <?php the_title() ?>">
                                <?php echo wp_trim_words(get_the_title(), 6) ?></a></h5>
                        <p class="thumb-list-item-desciption"><?php echo wp_trim_words(get_the_content(), 10); ?></p>
                    </div>
                </li>
                <?php
            endforeach;
            wp_reset_postdata();
            ?>
        </ul>
    </div>
<?php } ?>