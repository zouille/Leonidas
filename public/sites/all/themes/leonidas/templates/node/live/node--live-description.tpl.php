<article>
  <div class="about-video content-text">

    <?php if (!empty($accroche_title) || !empty($accroche)): ?>
      <div class="hangs">
      <?php if (!empty($accroche_title)): ?>
        <?php print $accroche_title; ?>
      <?php endif; ?>
      <?php if (!empty($accroche)): ?>
        <br/>
        <?php print $accroche; ?>
      <?php endif; ?>
      <?php if (!empty($signature_accroche)): ?>
        <br/>
        <?php print $signature_accroche; ?>
      <?php endif; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($description) || !empty($chapo)): ?>
      <?php if (!empty($chapo)): ?>
        <p class="chapo">
          <?php print $chapo; ?>
        </p>
      <?php endif; ?>
      <?php if (!empty($description)): ?>
        <p>
          <?php print $description; ?>
        </p>
      <?php endif; ?>
    <?php endif; ?>
    <?php if (!empty($automatic_description)): ?>
      <?php print $automatic_description; ?>
    <?php endif; ?>
    <h3>Distribution</h3>
    <dl>
      <?php if (!empty($place)): ?>
        <dt>Lieu :</dt>
        <dd><?php print $place; ?></dd>
      <?php endif; ?>
      <?php if (!empty($start_time)): ?>
        <dt>Date :</dt>
        <dd><?php print $start_time; ?></dd>
      <?php endif; ?>
      <?php if (!empty($duration)): ?>
        <dt>Durée :</dt>
        <dd><?php print $duration; ?></dd>
      <?php endif; ?>
      <?php if (!empty($category)): ?>
        <dt>Genre :</dt>
        <dd><?php print $category; ?></dd>
      <?php endif; ?>
      <?php if (!empty($festival)): ?>
        <dt>Festival :</dt>
        <dd><?php print $festival; ?></dd>
      <?php endif; ?>

      <?php foreach ($distribution_fields as $distribution_field_key => $distribution_field_value): ?>
        <?php if (!empty(${$distribution_field_key}[LANGUAGE_NONE])): ?>
          <dt><?php print $distribution_field_value; ?> :</dt>
          <dd>
            <?php $values = explode(',', ${$distribution_field_key}[LANGUAGE_NONE][0]['value']); ?>
            <?php foreach ($values as $key => $value): ?>
              <span class="vcard">
                <span class="fn">
                  <?php print $value; ?>
                </span>
              </span>
              <?php if ($key < count($values) - 1): ?>
                <span class="inline-separator"><?php print ',&nbsp;'; ?></span>
              <?php endif; ?>
            <?php endforeach; ?>
          </dd>
        <?php endif; ?>
      <?php endforeach; ?>

      <?php foreach ($actors_fields as $actors_field_key => $actors_field_value): ?>
        <?php if (!empty(${$actors_field_key}[LANGUAGE_NONE])): ?>
          <dt><?php print $actors_field_value; ?> :</dt>
          <dd>
            <?php $values = explode("\n", ${$actors_field_key}[LANGUAGE_NONE][0]['value']); ?>
            <?php foreach ($values as $key => $value): ?>
              <span class="vcard">
                <?php $role = ''; ?>
                <?php $matches = array(); ?>
                <?php preg_match('/\((.*)\)/', $value, $matches); ?>
                <?php $value = preg_replace('/\((.*)\)/', '', $value); ?>
                <span class="fn">
                  <?php print $value; ?>
                </span>
                <?php if (!empty($matches[1])): ?>
                  <?php $role = $matches[1]; ?>
                  <span>&nbsp;(</span><span class="role"><?php print $role; ?></span><span>)</span>
                <?php endif; ?>
              </span>
              <?php if ($key < count($values) - 1): ?>
                <br/>
              <?php endif; ?>
            <?php endforeach; ?>
          </dd>
        <?php endif; ?>
      <?php endforeach; ?>

      <?php if (!empty($field_live_execution[LANGUAGE_NONE])): ?>
        <dt>Réalisation :</dt>
        <dd>
          <?php $executions = explode(',', $field_live_execution[LANGUAGE_NONE][0]['value']); ?>
          <?php foreach ($executions as $key => $execution): ?>
            <span class="vcard">
              <span class="fn">
                <?php print $execution; ?>
              </span>
            </span>
            <?php if ($key < count($execution) - 1): ?>
              <span class="inline-separator"><?php print ',&nbsp;'; ?></span>
            <?php endif; ?>
          <?php endforeach; ?>
        </dd>
      <?php endif; ?>
      <?php if (!empty($field_live_production[LANGUAGE_NONE])): ?>
        <dt>Production :</dt>
        <dd><?php print $field_live_production[LANGUAGE_NONE][0]['value']; ?></dd>
      <?php endif; ?>
    </dl>
    <?php if (!empty($free_text)): ?>
      <?php foreach ($free_text as $item): ?>
        <dt><?php print "{$item['label']} :"; ?></dt>
        <dd><?php print $item['value']; ?></dd>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</article>
