# Changelog

All notable changes to the "Inventory & Asset Manager" plugin will be documented in this file.

## [v1.0.1] - 2026-11-14

### Fixed & Improved
- **100% Responsive UI Layout:** Full responsive compatibility across mobile, tablet, laptop, and desktop screens.
- **Scrollable Modals with Sticky Footer:** Fixed an issue where the "Save Item" button was cut off / hidden on smaller laptop and mobile screens. Modal headers and footers are now permanently pinned (`flex-shrink-0`), with the body smoothly scrollable (`overflow-y: auto`, `min-height: 0`).
- **Dynamic Viewport Height (`100dvh`):** Modals now respect mobile browser address bars and virtual keyboards without overflowing.
- **Form Grid System:** Optimized form rows (`col-12 col-md-6`, `col-6 col-md-3`, `col-12 col-sm-6 col-md-4`) so all fields stack gracefully on mobile screens without horizontal stretching.
- **Category & Challan Modals:** Applied standard responsive scrollable modals to Category Manager and Dual Copy A4 Gate Pass / Challan.

---

## [v1.0.0] - 2026-11-06

### Initial Release
- Serialized hardware tracking (Routers, ONUs, switches) with S/N & MAC.
- Bulk equipment tracking (Cables, connectors).
- Expense Management integration.
- Hardware deployment to customers and POP rooms.
- Damaged and faulty equipment reporting with 1-click replacement.
- Dual-copy printable A4 Delivery Challan / Gate Pass.
