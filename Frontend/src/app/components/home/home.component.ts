import { Component, OnInit, signal, WritableSignal } from "@angular/core";
import { IconComponent } from "../icon/icon.component";
import { delay } from "../../../helpers/timeout.helper";
import { ButtonComponent } from "../button/button.component";
import { AuthController } from "../../../controllers/auth.controller";
import { APP_ROUTES } from "../../../constants/route.constants";
import { RouteHandler } from "../../../services/route.handler";

@Component({
  selector: "app-home",
  imports: [IconComponent, ButtonComponent],
  templateUrl: "./home.component.html",
  styleUrl: "./home.component.css",
})
export class HomeComponent implements OnInit {
  protected readonly iconSize: number = 128;
  protected readonly brandName: WritableSignal<string> = signal("S");

  constructor(
    private readonly routeHandler: RouteHandler,
    protected readonly authController: AuthController
  ) {}

  async ngOnInit() {
    await this.animateBrandName();
  }

  protected goToLogin() {
    return this.routeHandler.goToRoute(APP_ROUTES.login);
  }

  protected goToDashboard() {
    return this.routeHandler.goToRoute(APP_ROUTES.storage);
  }

  private async animateBrandName() {
    const finalTitle = "Shelfy";
    const length = finalTitle.length;

    for (let i = 1; i < length; i++) {
      await delay(175);
      this.brandName.update((value) => value + finalTitle[i]);
    }
  }
}
