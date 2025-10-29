# Admin Features - To Be Implemented

## Menu Management
- **CRUD Operations for Menu Items**
  - Edit menu item: name, price, description, stock, image
  - Add new menu items
  - Delete menu items
  - Update stock levels in real-time

## Order Management Dashboard
- **View All Orders**
  - List all incoming orders from users
  - Filter by status: Not Ready, Ready, Packing, Success
  - Filter by order type: Pickup, Delivery
  - Filter by date range

- **Order Status Updates**
  - Change order status from "Not Ready" → "Ready" → "Success" (for Pickup)
  - Change order status from "Not Ready" → "Packing" → "Success" (for Delivery)
  - **Success** status archives order to history (completed)
  - Real-time status updates push to user's order info page

## Order Flow Logic

### Pickup Orders
1. User places order → Status: "Not Ready"
2. Admin marks as ready → Status: "Ready"
3. Customer picks up order
4. Admin marks as complete → Status: "Success"
5. Order archived to History (Completed Orders section)
6. Order removed from ongoing orders on menu dashboard

### Delivery Orders
1. User places order → Status: "Not Ready"
2. Admin marks as packing → Status: "Packing"
3. Rider picks up order
4. Rider delivers to customer
5. Admin confirms delivery → Status: "Success"
6. Order archived to History (Completed Orders section)
7. Order removed from ongoing orders on menu dashboard

### Status Flow Summary
- **Ongoing**: Not Ready, Ready, Packing (shown on menu dashboard)
- **Completed**: Success, Completed, Delivered (archived to history only)

## Technical Requirements

### Database Changes
```sql
-- Add payment_method column to orders table
ALTER TABLE orders 
ADD COLUMN payment_method VARCHAR(50) DEFAULT 'Transfer' AFTER delivery_cost;

-- Update default status
ALTER TABLE orders 
MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'Not Ready';
```

### Admin Routes
- `index.php?r=admin/dashboard` - Main admin dashboard
- `index.php?r=admin/orders` - Order management
- `index.php?r=admin/menu` - Menu management
- `index.php?r=admin/order/update` - Update order status (AJAX)

### Real-time Updates
- User order info page polls every 10 seconds for status updates
- Admin dashboard shows live order queue
- When admin changes status, user sees update within 10 seconds

### Status Polling
- User order info page polls every 10 seconds for status updates
- Polling stops automatically when order reaches Success/Completed/Delivered
- Page reloads to show completion message when status changes to Success
- Ongoing orders disappear from menu dashboard when marked as Success

## UI Components Needed
- Admin login/authentication
- Order queue dashboard with status badges
- Menu CRUD forms
- Status update buttons with confirmation
- Real-time notification system

## Security
- Admin-only routes protected by session check
- CSRF tokens for admin actions
- Input validation for all admin forms
- Stock validation to prevent negative values

## Future Enhancements
- Push notifications for new orders
- Order analytics and reports
- Revenue tracking
- Popular items dashboard
- Customer order history view for admin
