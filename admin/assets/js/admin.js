/**
 * Admin JavaScript for Yaxii WooCommerce Shipping Importer
 */

(function ($) {
  "use strict";

  let currentStep = 1;
  let wcData = null;
  let methodMapping = {};

  $(document).ready(function () {
    initWizard();
    bindEvents();
  });

  function initWizard() {
    showStep(1);
  }

  function bindEvents() {
    // Step 1: Scan
    $("#btn-scan").on("click", scanWooCommerce);
    $("#btn-continue-step2").on("click", () => goToStep(2));

    // Step 2: Method Mapping (both regular and sticky buttons)
    $("#btn-back-step1, #btn-back-step1-sticky").on("click", () => goToStep(1));
    $("#btn-continue-step3, #btn-continue-step3-sticky").on("click", () =>
      goToStep(3)
    );

    // Step 3: Import
    $("#btn-back-step2").on("click", () => goToStep(2));
    $("#btn-import").on("click", importData);

    // Step 4: Complete
    $("#btn-start-over").on("click", () => {
      location.reload();
    });
    $("#btn-export-csv").on("click", exportToCSV);

    // Backup Manager
    $("#btn-create-manual-backup").on("click", createManualBackup);
    $("#btn-refresh-backups").on("click", loadBackups);

    // Sticky actions bar visibility on scroll
    $(window).on("scroll", handleStickyActions);
  }

  function showStep(step) {
    currentStep = step;

    // Hide all steps
    $(".yaxii-wc-content").hide();
    $("#step-" + step).show();

    // Hide all sticky actions
    $(".yaxii-wc-sticky-actions").hide();

    // Update step indicators with completed status
    $(".yaxii-wc-step").removeClass("active").removeClass("completed");

    // Mark completed steps
    $(".yaxii-wc-step[data-step]").each(function () {
      const stepNum = parseInt($(this).data("step"));
      if (stepNum < step) {
        $(this).addClass("completed");
      } else if (stepNum === step) {
        $(this).addClass("active");
      }
    });

    // Scroll to top
    $("html, body").animate({ scrollTop: 0 }, 300);

    // Check sticky actions on step 2
    if (step === 2) {
      setTimeout(handleStickyActions, 100);
    }
  }

  function goToStep(step) {
    if (step === 2 && !wcData) {
      showError(
        yaxiiWcImporter.strings.error,
        "Please scan WooCommerce data first."
      );
      return;
    }

    if (step === 3) {
      // Collect method mapping
      methodMapping = {};
      $(".method-mapping-select").each(function () {
        const wcMethod = $(this).data("wc-method");
        const yaxiiMethod = $(this).val();
        methodMapping[wcMethod] = yaxiiMethod;
      });
    }

    showStep(step);
  }

  function scanWooCommerce() {
    showLoading(yaxiiWcImporter.strings.scanning);

    $.ajax({
      url: yaxiiWcImporter.ajaxUrl,
      type: "POST",
      data: {
        action: "yaxii_wc_scan",
        nonce: yaxiiWcImporter.nonce,
      },
      success: function (response) {
        hideLoading();

        if (response.success) {
          wcData = response.data;
          displayScanResults(response.data.stats, response.data.data);
        } else {
          showError(yaxiiWcImporter.strings.error, response.data.message);
        }
      },
      error: function (xhr) {
        hideLoading();
        showError(
          yaxiiWcImporter.strings.error,
          "AJAX request failed: " + xhr.statusText
        );
      },
    });
  }

  function displayScanResults(stats, data) {
    $("#stat-zones").text(stats.total_zones);
    $("#stat-methods").text(stats.total_methods);
    $("#stat-states").text(stats.total_states);
    $("#scan-results").slideDown();

    // Build method mapping UI
    buildMethodMapping(stats.methods_list);
  }

  function buildMethodMapping(methods) {
    const container = $("#method-mapping-container");
    container.empty();

    if (methods.length === 0) {
      container.html('<p class="description">No shipping methods found.</p>');
      return;
    }

    const yaxiiMethods = {
      home_delivery: "Home Delivery - التوصيل للمنزل",
      office_delivery: "Office Delivery - التوصيل للمكتب",
    };

    methods.forEach(function (method) {
      // Use detected_type if available, otherwise fallback
      const detectedType = method.detected_type || "unknown";
      const defaultMapping =
        detectedType === "home_delivery" || detectedType === "office_delivery"
          ? detectedType
          : getDefaultMapping(method.id);

      // Create unique identifier for this method
      const methodKey =
        detectedType + "_" + method.id + "_" + method.instance_id;

      // Show detection badge
      let detectionBadge = "";
      if (detectedType === "home_delivery") {
        detectionBadge =
          '<span style="color: #00a32a; font-size: 12px;">🏠 Auto-detected</span>';
      } else if (detectedType === "office_delivery") {
        detectionBadge =
          '<span style="color: #2271b1; font-size: 12px;">🏢 Auto-detected</span>';
      } else {
        detectionBadge =
          '<span style="color: #996800; font-size: 12px;">⚠️ Please select</span>';
      }

      const html = `
                <div class="method-mapping-item">
                    <div class="method-source">
                        <span class="method-label">WooCommerce Method</span>
                        <span class="method-value">${escapeHtml(
                          method.title
                        )} (${method.id})</span>
                        <span class="description">${
                          method.count
                        } instances | ${detectionBadge}</span>
                    </div>
                    <span class="method-arrow">→</span>
                    <div class="method-target">
                        <span class="method-label">Yaxii Method</span>
                        <select class="method-mapping-select" data-wc-method="${methodKey}">
                            ${Object.keys(yaxiiMethods)
                              .map(
                                (key) =>
                                  `<option value="${key}" ${
                                    key === defaultMapping ? "selected" : ""
                                  }>${yaxiiMethods[key]}</option>`
                              )
                              .join("")}
                        </select>
                    </div>
                </div>
            `;

      container.append(html);
    });
  }

  function getDefaultMapping(wcMethodId) {
    const defaults = {
      flat_rate: "home_delivery",
      free_shipping: "home_delivery",
      local_pickup: "office_delivery",
    };

    return defaults[wcMethodId] || "home_delivery";
  }

  function importData() {
    if (!confirm(yaxiiWcImporter.strings.confirm_import)) {
      return;
    }

    const strategy = $('input[name="strategy"]:checked').val();
    const createBackup = $("#create-backup").is(":checked");

    showLoading(yaxiiWcImporter.strings.importing);

    $.ajax({
      url: yaxiiWcImporter.ajaxUrl,
      type: "POST",
      data: {
        action: "yaxii_wc_import",
        nonce: yaxiiWcImporter.nonce,
        method_mapping: JSON.stringify(methodMapping),
        strategy: strategy,
        create_backup: createBackup,
      },
      success: function (response) {
        hideLoading();

        if (response.success) {
          displayImportReport(response.data);
          goToStep(4);
        } else {
          showError(yaxiiWcImporter.strings.error, response.data.message);
        }
      },
      error: function (xhr) {
        hideLoading();
        showError(
          yaxiiWcImporter.strings.error,
          "AJAX request failed: " + xhr.statusText
        );
      },
    });
  }

  function displayImportReport(data) {
    const stats = data.stats;
    const backupId = data.backup_id;

    let html = '<div class="yaxii-wc-notice notice-success">';
    html += '<span class="dashicons dashicons-yes-alt"></span>';
    html += "<div>";
    html += "<p><strong>" + data.message + "</strong></p>";
    html += "</div>";
    html += "</div>";

    html += '<div class="report-stat">';
    html += '<span class="report-label">Total States Processed:</span>';
    html += '<span class="report-value">' + stats.total_states + "</span>";
    html += "</div>";

    html += '<div class="report-stat">';
    html += '<span class="report-label">New States Added:</span>';
    html += '<span class="report-value">' + stats.new_states + "</span>";
    html += "</div>";

    html += '<div class="report-stat">';
    html += '<span class="report-label">Updated States:</span>';
    html += '<span class="report-value">' + stats.updated_states + "</span>";
    html += "</div>";

    html += '<div class="report-stat">';
    html += '<span class="report-label">Total Shipping Costs:</span>';
    html += '<span class="report-value">' + stats.total_costs + "</span>";
    html += "</div>";

    if (backupId) {
      html += '<div class="report-stat">';
      html += '<span class="report-label">Backup Created:</span>';
      html += '<span class="report-value">' + backupId + "</span>";
      html += "</div>";
    }

    $("#import-report").html(html);
  }

  function exportToCSV() {
    showLoading("Generating CSV...");

    $.ajax({
      url: yaxiiWcImporter.ajaxUrl,
      type: "POST",
      data: {
        action: "yaxii_wc_export_csv",
        nonce: yaxiiWcImporter.nonce,
      },
      success: function (response) {
        hideLoading();

        if (response.success) {
          downloadCSV(response.data.csv, response.data.filename);
          showSuccess("CSV exported successfully!");
        } else {
          showError(yaxiiWcImporter.strings.error, response.data.message);
        }
      },
      error: function (xhr) {
        hideLoading();
        showError(
          yaxiiWcImporter.strings.error,
          "AJAX request failed: " + xhr.statusText
        );
      },
    });
  }

  function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);

    link.setAttribute("href", url);
    link.setAttribute("download", filename);
    link.style.visibility = "hidden";

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  function createManualBackup() {
    showLoading("Creating backup...");

    $.ajax({
      url: yaxiiWcImporter.ajaxUrl,
      type: "POST",
      data: {
        action: "yaxii_wc_create_backup",
        nonce: yaxiiWcImporter.nonce,
      },
      success: function (response) {
        hideLoading();

        if (response.success) {
          showSuccess(response.data.message);
          loadBackups();
        } else {
          showError(yaxiiWcImporter.strings.error, response.data.message);
        }
      },
      error: function (xhr) {
        hideLoading();
        showError(
          yaxiiWcImporter.strings.error,
          "AJAX request failed: " + xhr.statusText
        );
      },
    });
  }

  function loadBackups() {
    showLoading("Loading backups...");

    $.ajax({
      url: yaxiiWcImporter.ajaxUrl,
      type: "POST",
      data: {
        action: "yaxii_wc_get_backups",
        nonce: yaxiiWcImporter.nonce,
      },
      success: function (response) {
        hideLoading();

        if (response.success) {
          displayBackups(response.data.backups);
        } else {
          showError(yaxiiWcImporter.strings.error, response.data.message);
        }
      },
      error: function (xhr) {
        hideLoading();
        showError(
          yaxiiWcImporter.strings.error,
          "AJAX request failed: " + xhr.statusText
        );
      },
    });
  }

  function displayBackups(backups) {
    const container = $("#backups-list");
    container.empty();

    if (backups.length === 0) {
      container.html('<p class="description">No backups found.</p>');
      return;
    }

    backups.forEach(function (backup) {
      const html = `
                <div class="backup-item">
                    <div class="backup-info">
                        <div class="backup-id">${escapeHtml(backup.id)}</div>
                        <div class="backup-meta">
                            Date: ${escapeHtml(backup.date)} | 
                            States: ${backup.size}
                        </div>
                    </div>
                    <div class="backup-actions">
                        <button type="button" class="button btn-restore-backup" data-backup-id="${
                          backup.id
                        }">
                            <span class="dashicons dashicons-update"></span>
                            Restore
                        </button>
                    </div>
                </div>
            `;

      container.append(html);
    });

    // Bind restore buttons
    $(".btn-restore-backup").on("click", function () {
      const backupId = $(this).data("backup-id");
      restoreBackup(backupId);
    });
  }

  function restoreBackup(backupId) {
    if (!confirm(yaxiiWcImporter.strings.confirm_restore)) {
      return;
    }

    showLoading("Restoring backup...");

    $.ajax({
      url: yaxiiWcImporter.ajaxUrl,
      type: "POST",
      data: {
        action: "yaxii_wc_restore_backup",
        nonce: yaxiiWcImporter.nonce,
        backup_id: backupId,
      },
      success: function (response) {
        hideLoading();

        if (response.success) {
          showSuccess(response.data.message);
        } else {
          showError(yaxiiWcImporter.strings.error, response.data.message);
        }
      },
      error: function (xhr) {
        hideLoading();
        showError(
          yaxiiWcImporter.strings.error,
          "AJAX request failed: " + xhr.statusText
        );
      },
    });
  }

  function showLoading(message) {
    $("#loading-message").text(message || "Processing...");
    $("#loading-overlay").fadeIn(200);
  }

  function hideLoading() {
    $("#loading-overlay").fadeOut(200);
  }

  function showError(title, message) {
    const html = `
            <div class="notice notice-error is-dismissible">
                <p><strong>${escapeHtml(title)}:</strong> ${escapeHtml(
      message
    )}</p>
            </div>
        `;

    $(".yaxii-wc-importer-wrap > h1").after(html);

    setTimeout(function () {
      $(".notice.is-dismissible").fadeOut();
    }, 5000);
  }

  function showSuccess(message) {
    const html = `
            <div class="notice notice-success is-dismissible">
                <p><strong>${escapeHtml(message)}</strong></p>
            </div>
        `;

    $(".yaxii-wc-importer-wrap > h1").after(html);

    setTimeout(function () {
      $(".notice.is-dismissible").fadeOut();
    }, 5000);
  }

  function escapeHtml(text) {
    const map = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
    };
    return String(text).replace(/[&<>"']/g, function (m) {
      return map[m];
    });
  }

  function handleStickyActions() {
    // Only show sticky actions on step 2
    if (currentStep !== 2) {
      $("#sticky-actions-step2").hide();
      return;
    }

    const container = $("#method-mapping-container");
    const regularActions = $("#step-2 .yaxii-wc-actions").first();

    if (container.length === 0 || regularActions.length === 0) {
      return;
    }

    const containerHeight = container.outerHeight();
    const scrollTop = $(window).scrollTop();
    const windowHeight = $(window).height();
    const actionsOffset = regularActions.offset().top;

    // Show sticky bar if:
    // 1. Container has more than 5 items (likely needs scrolling)
    // 2. Regular actions are below the fold
    const methodCount = container.find(".method-mapping-item").length;

    if (methodCount > 5 && actionsOffset > scrollTop + windowHeight - 100) {
      $("#sticky-actions-step2").fadeIn(200);
    } else {
      $("#sticky-actions-step2").fadeOut(200);
    }
  }
})(jQuery);
