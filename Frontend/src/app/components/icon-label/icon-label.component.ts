import { Component, Input } from "@angular/core";
import { APP_ROUTES, AppRoute } from "../../../constants/route.constants";
import { RouteHandler } from "../../../handlers/route.handler";
import { NgClass } from "@angular/common";

@Component({
  selector: "app-icon-label",
  imports: [NgClass],
  templateUrl: "./icon-label.component.html",
  styleUrl: "./icon-label.component.css",
})
export class IconLabelComponent {
  @Input({ required: true })
  public route: AppRoute = APP_ROUTES.home;

  @Input({ required: true })
  public iconName?: string;

  @Input({ required: true })
  public label?: string;

  constructor(private readonly routeHandler: RouteHandler) {}

  protected isOnRoute() {
    return this.routeHandler.isOnRoute(this.route);
  }

  protected goToRoute() {
    return this.routeHandler.goToRoute(this.route);
  }
}
