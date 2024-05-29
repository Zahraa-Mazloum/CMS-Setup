// base
export type Color = "primary" | "secondary" | "success" | "danger" | "warning" | "info" | "light" | "dark"
export type BreakPoint = "sm" | "md" | "lg" | "xl" | "xxl"

// spinners
export type SpinnerType = "border" | "grow"
export type SpinnerColor = Color

// buttons
export type ButtonColor = Color | "link" | string
export type ButtonType = "button" | "submit" | "reset"
export type ButtonSize = "sm" | "lg"

export type ButtonGroupDirection = "horizontal" | "vertical"

// badges
export type BadgeColor = Color

// alert
export type AlertColor = Color

// modal
export type ModalSize = "sm" | "lg" | "xl"
export type ModalFullscreen = BreakPoint

// offcanvas
export type OffcanavsPlacement = "start" | "end" | "top" | "bottom"
export type OffcanavsResponsive = BreakPoint

// dropdown
export type DropdownDirection = "start" | "end" | "up" | "down"
export type DropdownAlignment = "start" | "end"

// progress
export type ProgressColor = Color

// list group
export type ListGroupColor = Color

// toast
export type ToastPosition = 'top-start' | 'top-center' | 'top-end' | 'center-start' | 'center' | 'center-end' | 'bottom-start' | 'bottom-center' | 'bottom-end'
export type ToastColor = Color
