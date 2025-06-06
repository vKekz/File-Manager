import { Component, Input } from "@angular/core";
import { RouteHandler } from "../../../handlers/route.handler";
import { APP_ROUTES } from "../../../constants/route.constants";

@Component({
  selector: "app-icon",
  imports: [],
  templateUrl: "./icon.component.html",
  styleUrl: "./icon.component.css",
})
export class IconComponent {
  @Input()
  public width: number = 256;

  @Input()
  public height: number = 256;

  constructor(private readonly routeHandler: RouteHandler) {}

  protected goToHome() {
    return this.routeHandler.goToRoute(APP_ROUTES.home);
  }
}
