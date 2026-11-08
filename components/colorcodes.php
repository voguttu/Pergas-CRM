<?php
function renderColorCodes($interactive = false, $resetLabel = 'Clear filters') {
    if ($interactive) {
        $statuses = array(
            'visited' => 'Visited',
            'revisited' => 'Revisited',
            'partialy' => 'Partialy',
            'bought' => 'Bought',
            'showroom' => 'Showroom',
            'quoted' => 'Quoted',
            'failed' => 'Failed',
        );

        echo "<div class='status-filter-legend' role='group' aria-label='Filter leads by status'>";
        foreach ($statuses as $status => $label) {
            $escapedStatus = htmlspecialchars($status, ENT_QUOTES, 'UTF-8');
            $escapedLabel = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
            echo "<button type='button' class='status-filter-button' data-status-filter='$escapedStatus' aria-pressed='true'>
                    <span class='status-filter-swatch table-$escapedStatus' aria-hidden='true'></span>
                    <span>$escapedLabel</span>
                  </button>";
        }
        $escapedResetLabel = htmlspecialchars($resetLabel, ENT_QUOTES, 'UTF-8');
        echo "<button type='button' class='status-filter-reset' data-lead-filter-reset>$escapedResetLabel</button>
              <span class='status-filter-count' data-status-filter-count aria-live='polite'></span>
              </div>";
        return;
    }

    echo "
    <div>
        <div class='color-box table-visited'></div>
        <div class='color-box table-revisited'></div>
        <div class='color-box table-partialy'></div>
        <div class='color-box table-bought'></div>
        <div class='color-box table-showroom'></div>
        <div class='color-box table-quoted'></div>
        <div class='color-box table-failed'></div>
    </div>

    <div>
        <div class='color-name'>Visited</div>
        <div class='color-name'>Revisited</div>
        <div class='color-name'>Partialy</div>
        <div class='color-name'>Bought</div>
        <div class='color-name'>Showroom</div>
        <div class='color-name'>Quoted</div>
        <div class='color-name'>Failed</div>
    </div>
    ";
}
?>
