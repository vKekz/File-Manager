import { Component } from "@angular/core";
import { IconLabelComponent } from "../icon-label/icon-label.component";
import { APP_ROUTES } from "../../../constants/route.constants";
import { CreateButtonComponent } from "../create-button/create-button.component";
import { RouteHandler } from "../../../handlers/route.handler";

@Component({
  selector: "app-main-bottom-nav",
  imports: [IconLabelComponent, CreateButtonComponent],
  templateUrl: "./main-bottom-nav.component.html",
  styleUrl: "./main-bottom-nav.component.css",
})
export class MainBottomNavComponent {
  protected readonly ROUTES = APP_ROUTES;
  protected readonly APP_ROUTES = APP_ROUTES;

  constructor(protected readonly routeHandler: RouteHandler) {}
}
