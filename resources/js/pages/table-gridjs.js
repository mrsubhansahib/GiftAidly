/*
Template Name: Taplox- Responsive Bootstrap 5 Admin Dashboard
Author: Stackbros
File: datatable js
*/
import gridjs  from 'gridjs/dist/gridjs.umd.js';

class GridDatatable {

     init() {
          this.GridjsTableInit();
     }

     GridjsTableInit() {

          (() => {
          const el = document.getElementById("table-gridjs");
          if (!el) return;

          const parse = (v, fb) => { try { return JSON.parse(v ?? ""); } catch { return fb; } };
          let rows = parse(el.dataset.rows, []);
          let cols = parse(el.dataset.columns, []);
          const limit = Number.parseInt(el.dataset.limit || "10", 10) || 10;

          if (!Array.isArray(rows)) rows = [];

          // Infer columns & normalize data
          let columns = [];
          let data = rows;

          if (Array.isArray(cols) && cols.length) {
          columns = cols;
          if (rows.length && !Array.isArray(rows[0]) && typeof rows[0] === "object") {
               data = rows.map(r => cols.map(k => r?.[k]));
          }
          } else if (rows.length) {
          const first = rows[0];
          if (Array.isArray(first)) {
               const max = rows.reduce((m, r) => Math.max(m, Array.isArray(r) ? r.length : 0), 0);
               columns = Array.from({ length: max }, (_, i) => `Col ${i + 1}`);
          } else if (typeof first === "object" && first) {
               const keys = Object.keys(first);
               columns = keys;
               data = rows.map(r => keys.map(k => r?.[k]));
          } else {
               columns = [];
               data = [];
          }
          }

          // Build grid columns (with safe HTML & Detail handling)
          const gridColumns = (columns || []).map((c) => {
          const name = typeof c === "string" ? c : (c?.name ?? "");
          if (name === "Detail") {
               return {
               name,
               formatter: (cell, row) => {
                    if (cell && typeof cell === "object") {
                    const { text = "Detail", url = "#", color = "primary" } = cell;
                    return gridjs.html(`<a href="${url}" class="btn btn-sm btn-${color}">${text}</a>`);
                    }
                    const id = row?.cells?.[0]?.data ?? "";
                    return gridjs.html(`<a href="/detail/${id}" class="btn btn-sm btn-primary">Detail</a>`);
               },
               };
          }
          return {
               name,
               formatter: (cell) =>
               (typeof cell === "string" && cell.trim().startsWith("<")) ? gridjs.html(cell) : (cell ?? "")
          };
          });

          // Render
          el.innerHTML = "";
               new gridjs.Grid({
               columns: gridColumns,
               data,
               search: true,
               sort: true,
               pagination: { limit }
               }).render(el);
          })();
     }

}

document.addEventListener('DOMContentLoaded', function (e) {
     new GridDatatable().init();
});

document.addEventListener("livewire:load", () => {
  Livewire.hook("message.processed", () => {
    new GridDatatable().init();
  });
});